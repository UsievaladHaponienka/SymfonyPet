<?php

namespace App\Controller\Group;

use App\Entity\Album;
use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupFormType;
use App\Form\PostFormType;
use App\Form\SearchFormType;
use App\Repository\AlbumRepository;
use App\Repository\GroupRepository;
use App\Service\ImageProcessor;
use App\Service\SearchService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    private GroupRepository $groupRepository;
    private ImageProcessor $imageProcessor;
    private AlbumRepository $albumRepository;
    private SearchService $searchService;

    public function __construct(
        GroupRepository $groupRepository,
        AlbumRepository $albumRepository,
        ImageProcessor  $imageProcessor,
        SearchService   $searchService
    )
    {
        $this->groupRepository = $groupRepository;
        $this->imageProcessor = $imageProcessor;
        $this->albumRepository = $albumRepository;
        $this->searchService = $searchService;
    }

    #[Route('groups', name: 'group_index')]
    public function index(Request $request): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupFormType::class, $group);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createGroup($form, $group);

            return $this->redirectToRoute('group_index');
        }

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);
        $groupSearchResult = null;

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $groupSearchResult = $this->searchService->searchGroups(
                $searchForm->get('search_string')->getData()
            );
        }

        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfile();

        if ($profile) {
            return $this->render('group/index.html.twig', [
                'profile' => $profile,
                'groupSearchResult' => $groupSearchResult,
                'groupForm' => $form->createView(),
                'searchForm' => $searchForm->createView()
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/{groupId}', name: 'group_show')]
    public function show(int $groupId): Response
    {
        $group = $this->groupRepository->find($groupId);

        if ($group) {
            //TODO handle exceptions
            $postForm = $this->createForm(
                PostFormType::class,
                null, [
                'action' => $this->generateUrl('post_create_group', ['groupId' => $groupId]),
                'method' => 'POST',
            ]);

            return $this->render('group/show.html.twig', [
                'group' => $group,
                'postForm' => $postForm->createView()
            ]);
        }

        throw $this->createNotFoundException();
    }

    protected function createGroup($form, Group $group): void
    {
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

        $album = $this->createDefaultGroupAlbum();
        $album->setGroup($group);

        $this->albumRepository->save($album);
        $this->groupRepository->save($group, true);
    }

    protected function createDefaultGroupAlbum(): Album
    {
        $defaultGroupAlbum = new Album();
        $defaultGroupAlbum->setType(Album::GROUP_DEFAULT_TYPE);
        $defaultGroupAlbum->setTitle(Album::DEFAULT_ALBUM_TITLE);

        return $defaultGroupAlbum;
    }
}
