<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Group;
use App\Entity\Post;
use App\Entity\User;
use App\Form\GroupFormType;
use App\Form\PostFormType;
use App\Repository\AlbumRepository;
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
    private AlbumRepository $albumRepository;

    public function __construct(
        GroupRepository $groupRepository,
        ProfileRepository $profileRepository,
        AlbumRepository $albumRepository,
        ImageProcessor $imageProcessor
    )
    {
        $this->groupRepository = $groupRepository;
        $this->profileRepository = $profileRepository;
        $this->imageProcessor = $imageProcessor;
        $this->albumRepository = $albumRepository;
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

            $album = $this->getDefaultGroupAlbum();
            $album->setGroup($group);

            $this->albumRepository->save($album);
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

    #[Route('group/edit/{groupId}', name: 'group_edit')]
    public function edit(Request $request, int $groupId): Response
    {
        $group = $this->groupRepository->find($groupId);

        if($group && $this->isActionAllowed($group)) {
            $form = $this->createForm(GroupFormType::class, $group);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $group->setTitle($form->get('title')->getData());
                $group->setDescription($form->get('description')->getData());
                $group->setType($form->get('type')->getData());

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

                return $this->redirectToRoute('group_show', ['groupId' => $group->getId()]);
            }

            return $this->render('group/edit.html.twig', [
                'group' => $group,
                'groupEditForm' => $form->createView()
            ]);
        }

        throw $this->createNotFoundException();
    }
    #[Route('group/confirm-delete/{groupId}', name: 'group_confirm_delete')]
    public function confirmDelete(int $groupId): Response
    {
        $group = $this->groupRepository->find($groupId);

        if ($group && $this->isActionAllowed($group)) {
            return $this->render('group/confirm-delete.html.twig', [
                'group' => $group
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/delete/{groupId}', name: 'group_delete')]
    public function delete(int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);

        if ($group && $this->isActionAllowed($group)) {
            $this->groupRepository->remove($group, true);

            return $this->redirectToRoute('group_index', [
                'profileId' => $user->getProfile()->getId()
            ]);
        }

        throw $this->createNotFoundException();
    }


    protected function getDefaultGroupAlbum(): Album
    {
        $defaultGroupAlbum = new Album();
        $defaultGroupAlbum->setType(Album::GROUP_DEFAULT_TYPE);
        $defaultGroupAlbum->setTitle(Album::DEFAULT_ALBUM_TITLE);

        return $defaultGroupAlbum;
    }

    protected function isActionAllowed(Group $group): bool
    {
        /** @var User $user */
        $user = $this->getUser();
        return $user->getProfile()->getId() == $group->getAdmin()->getId();
    }
}
