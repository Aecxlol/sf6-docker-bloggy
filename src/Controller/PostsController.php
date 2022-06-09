<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
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

        return $this->render('posts/index.html.twig', [
            'posts' => $posts
        ]);
    }
}
