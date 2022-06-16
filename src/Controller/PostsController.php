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
     */
    public function __construct(private PostRepository $postRepository) {}

    /**
     * @param PostRepository $postRepository
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $this->postRepository->findAllPublished();

        return $this->render('posts/index.html.twig', compact('posts'));
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @param string $slug
     * @return Response
     */
    #[Route('/posts/{year}/{month}/{day}/{slug}', name: 'app_posts_show')]
    public function show(int $year, int $month, int $day, string $slug): Response
    {
        $post = $this->postRepository->findOneByPublishedDateAndSlug($year, $month, $day, $slug);

        return $this->render('posts/show.html.twig', compact('post'));
    }
}
