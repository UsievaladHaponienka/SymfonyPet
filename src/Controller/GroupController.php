<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Group;
use App\Entity\Interface\InteractiveEntityInterface as IEInterface;
use App\Entity\User;
use App\Form\GroupFormType;
use App\Form\PostFormType;
use App\Form\SearchFormType;
use App\Repository\GroupRepository;
use App\Repository\ProfileRepository;
use App\Service\ImageProcessor;
use App\Service\SearchService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    /**
     * @param GroupRepository $groupRepository
     * @param ProfileRepository $profileRepository
     * @param ImageProcessor $imageProcessor
     * @param SearchService $searchService
     */
    public function __construct(
        private readonly GroupRepository   $groupRepository,
        private readonly ProfileRepository $profileRepository,
        private readonly ImageProcessor    $imageProcessor,
        private readonly SearchService     $searchService
    )
    {
    }

    #[Route('groups/{profileId}', name: 'group_index', methods: ['GET', 'POST'])]
    public function index(Request $request, int $profileId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $this->profileRepository->find($profileId);

        if ($profile && $profile->getPrivacySettings()->isGroupListViewAllowed($user->getProfile())) {
            $createGroupForm = $this->createForm(GroupFormType::class);
            $createGroupForm->handleRequest($request);

            if ($createGroupForm->isSubmitted() && $createGroupForm->isValid()) {
                $this->saveGroup($createGroupForm);
            }

            $searchForm = $this->createForm(SearchFormType::class);
            $searchForm->handleRequest($request);
            $groupSearchResult = null;

            if ($searchForm->isSubmitted() && $searchForm->isValid()) {
                $groupSearchResult = $this->searchService->searchGroups(
                    $searchForm->get('search_string')->getData()
                );
            }

            return $this->render('group/index.html.twig', [
                'profile' => $profile,
                'groupSearchResult' => $groupSearchResult,
                'groupForm' => $createGroupForm->createView(),
                'searchForm' => $searchForm->createView()
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/{groupId}', name: 'group_show',methods: ['GET'])]
    public function show(int $groupId): Response
    {
        $group = $this->groupRepository->find($groupId);

        if ($group) {
            $postForm = $this->createForm(
                PostFormType::class, null, [
                'action' => $this->generateUrl('post_create_group', ['groupId' => $groupId])
            ]);

            return $this->render('group/show.html.twig', [
                'group' => $group,
                'postForm' => $postForm->createView()
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/edit/{groupId}', name: 'group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);

        if ($group && $group->isActionAllowed($user->getProfile(), IEInterface::EDIT_ACTION_CODE)) {
            $groupEditForm = $this->createForm(GroupFormType::class, $group);
            $groupEditForm->handleRequest($request);

            if ($groupEditForm->isSubmitted() && $groupEditForm->isValid()) {
                $this->saveGroup($groupEditForm, $group);
                return $this->redirectToRoute('group_show', ['groupId' => $group->getId()]);
            }

            $profileSearchForm = $this->createForm(SearchFormType::class);
            $profileSearchForm->handleRequest($request);
            $profileSearchResult = null;

            if ($profileSearchForm->isSubmitted() && $profileSearchForm->isValid()) {
                $profileSearchResult = $this->searchService->searchProfiles(
                    $profileSearchForm->get('search_string')->getData()
                );
            }

            return $this->render('group/edit.html.twig', [
                'group' => $group,
                'groupEditForm' => $groupEditForm->createView(),
                'profileSearchForm' => $profileSearchForm->createView(),
                'profileSearchResult' => $profileSearchResult
            ]);
        }

        throw $this->createNotFoundException();
    }

    #[Route('group/delete/{groupId}', name: 'group_delete', methods: ['DELETE'])]
    public function delete(int $groupId): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = $this->groupRepository->find($groupId);

        if ($group && $group->isActionAllowed($user->getProfile(), IEInterface::DELETE_ACTION_CODE)) {
            $this->groupRepository->remove($group, true);

            return new JsonResponse([
                'redirectUrl' => $this->generateUrl('group_index', [
                    'profileId' => $group->getAdmin()->getId()
                ])
            ]);
        }

        throw $this->createNotFoundException();
    }

    /**
     * If group is passed, update group using data from $form.
     * If no group is passed, create new $group using data from $form.
     *
     * @param FormInterface $form
     * @param Group|null $group
     * @return void
     */
    protected function saveGroup(FormInterface $form, Group $group = null): void
    {
        if (!$group) {
            /** @var User $user */
            $user = $this->getUser();
            $adminProfile = $user->getProfile();

            $group = new Group();
            $group->setAdmin($adminProfile);
            $group->addProfile($adminProfile);
            $group->setCreatedAt(new DateTimeImmutable());

            $defaultGroupAlbum = new Album();
            $defaultGroupAlbum->setType(Album::GROUP_DEFAULT_TYPE);
            $defaultGroupAlbum->setTitle(Album::DEFAULT_ALBUM_TITLE);

            $group->addAlbum($defaultGroupAlbum);
        }

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
    }
}
