<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\NonUniqueResultException;
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
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
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
     * @throws NonUniqueResultException
     */
    #[Route('/posts/{year}/{month}/{day}/{slug}', name: 'app_posts_show')]
    public function show(int $year, int $month, int $day, string $slug): Response
    {
        $post = $this->postRepository->findOneByPublishedDateAndSlug($year, $month, $day, $slug);

        if(is_null($post)){
            throw $this->createNotFoundException("Post not found");
        }

        return $this->render('posts/show.html.twig', compact('post'));
    }
}
