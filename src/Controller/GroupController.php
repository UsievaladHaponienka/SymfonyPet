<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Post;
use App\Entity\User;
use App\Form\GroupFormType;
use App\Form\PostFormType;
use App\Repository\GroupRepository;
use App\Repository\ProfileRepository;
use App\Service\ImageProcessor;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    private GroupRepository $groupRepository;
    private ProfileRepository $profileRepository;
    private ImageProcessor $imageProcessor;

    public function __construct(
        GroupRepository $groupRepository,
        ProfileRepository $profileRepository,
        ImageProcessor $imageProcessor
    )
    {
        $this->groupRepository = $groupRepository;
        $this->profileRepository = $profileRepository;
        $this->imageProcessor = $imageProcessor;
    }

    #[Route('/groups/{profileId}', name: 'group_index')]
    public function index(int $profileId): Response
    {
        $profile = $this->profileRepository->find($profileId);

        if ($profile) {
            return $this->render('group/index.html.twig', [
                'profile' => $profile,
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/create', name: 'group_create')]
    public function create(Request $request): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupFormType::class, $group);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $adminProfile = $user->getProfile();

            $group->setTitle($form->get('title')->getData());
            $group->setType($form->get('type')->getData());
            $group->setDescription($form->get('description')->getData());
            $group->setAdmin($adminProfile);
            $group->addProfile($adminProfile);
            $group->setCreatedAt(new DateTimeImmutable());

            $image = $form->get('group_image_url')->getData();
            if ($image) {
                $newFileName = $this->imageProcessor->saveImage(
                    $image,
                    ImageProcessor::PROFILE_IMAGE_TYPE,
                    '/public/images/group/'
                );
                $group->setGroupImageUrl('/images/group/' . $newFileName);
            }

            $this->groupRepository->save($group, true);

            return $this->redirectToRoute('group_index', ['profileId' => $adminProfile->getId()]);
        }

        return $this->render('group/create.html.twig',[
            'groupForm' => $form->createView()
        ]);
    }

    #[Route('group/{groupId}', name: 'group_show')]
    public function show(int $groupId): Response
    {
        $group = $this->groupRepository->find($groupId);

        if($group){
            //TODO handle exceptions
            $postForm = $this->createForm(
                PostFormType::class,
                null, [
                'action' => $this->generateUrl('post_create_group', ['groupId' => $groupId]),
                'method' => 'POST',
            ]);

            return $this->render('group/show.html.twig',[
                'group' => $group,
                'postForm' => $postForm->createView()
            ]);
        }

        throw $this->createNotFoundException();
    }
}
