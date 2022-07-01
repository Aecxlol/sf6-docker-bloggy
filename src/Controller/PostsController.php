<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\SharePostFormType;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class PostsController extends AbstractController
{
    /**
     * @param PostRepository $postRepository
     */
    public function __construct(private PostRepository $postRepository)
    {
    }

    /**
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $query = $this->postRepository->getAllPublishedArticlesQuery();

        $page = $request->query->getInt('page', 1);

        $pagination = $paginator->paginate($query, $page, Post::LIMIT_PER_PAGE, [
            PaginatorInterface::PAGE_OUT_OF_RANGE => PaginatorInterface::PAGE_OUT_OF_RANGE_FIX
        ]);

        return $this->render('posts/index.html.twig', compact('pagination'));
    }

    /**
     * @param DateTimeImmutable $date
     * @param string $slug
     * @return Response
     * @throws NonUniqueResultException
     */
    #[Route('/posts/{date}/{slug}',
        name: 'app_posts_show',
        requirements: [
            'date' => Requirement::DATE_YMD,
            'slug' => Requirement::ASCII_SLUG
        ],
        methods: ['GET']
    )]
    public function show(DateTimeImmutable $date, string $slug): Response
    {
        $post = $this->postRepository->findOneByPublishedDateAndSlug($date, $slug);

        if (is_null($post)) {
            throw $this->createNotFoundException("Post not found");
        }

        return $this->render('posts/show.html.twig', compact('post'));
    }

    /**
     * @param DateTimeImmutable $date
     * @param string $slug
     * @return Response
     * @throws NonUniqueResultException
     */
    #[Route('/posts/{date}/{slug}/share',
        name: 'app_posts_share',
        requirements: [
            'date' => Requirement::DATE_YMD,
            'slug' => Requirement::ASCII_SLUG
        ],
        methods: ['GET', 'POST']
    )]
    public function share(DateTimeImmutable $date, string $slug): Response
    {
        $post = $this->postRepository->findOneByPublishedDateAndSlug($date, $slug);

        if (is_null($post)) {
            throw $this->createNotFoundException("Post not found");
        }

        $shareForm = $this->createForm(SharePostFormType::class);

        return $this->renderForm('posts/share.html.twig', compact('shareForm', 'post'));
    }
}
