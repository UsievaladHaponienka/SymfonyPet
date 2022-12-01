<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedController extends AbstractController
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    #[Route('/feed', name: 'feed_index')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfile();

        $posts = $this->collectPosts($profile);
        usort($posts, function($e1, $e2) {
            /** @var Post $e1 */
            /** @var Post $e2 */
            return $e1->getCreatedAt() > $e2->getCreatedAt() ? -1 : 1;
        });

        return $this->render('feed/index.html.twig', [
            'posts' => $posts
        ]);

    }

    protected function collectPosts(Profile $profile): array
    {
        $groupIds = [];
        foreach ($profile->getGroups() as $group) {
            $groupIds[] = $group->getId();
        }

        $groupPosts = $this->postRepository->findBy([
            'type' => Post::GROUP_POST_TYPE,
            'group' => $groupIds
        ]);

        $friendIds = [];
        foreach ($profile->getFriendships() as $friendship) {
            $friendIds[] = $friendship->getFriend()->getId();
        }

        $profilePosts = $this->postRepository->findBy([
            'type' => Post::USER_POST_TYPE,
            'profile' => $friendIds
        ]);

        return array_merge($groupPosts, $profilePosts);
    }
}
