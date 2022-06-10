<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    /**
     * @param PostRepository $postRepository
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
    {
        /**
         * @todo filter to select published posts
         * @todo order by publishedAt DESC
         */
        $posts = $postRepository->findAll();

        return $this->render('posts/index.html.twig', compact('posts'));
    }

    /**
     * @param Post $post
     * @return Response
     */
    #[Route('/posts/{slug}', name: 'app_posts_show')]
    public function show(Post $post): Response
    {
        return $this->render('posts/show.html.twig', compact('post'));
    }
}
