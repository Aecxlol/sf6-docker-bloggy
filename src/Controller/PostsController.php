<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\SharePostFormType;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
     * @param Request $request
     * @param MailerInterface $mailer
     * @param DateTimeImmutable $date
     * @param string $slug
     * @return Response
     * @throws NonUniqueResultException
     * @throws TransportExceptionInterface
     */
    #[Route('/posts/{date}/{slug}/share',
        name: 'app_posts_share',
        requirements: [
            'date' => Requirement::DATE_YMD,
            'slug' => Requirement::ASCII_SLUG
        ],
        methods: ['GET', 'POST']
    )]
    public function share(Request $request, MailerInterface $mailer, DateTimeImmutable $date, string $slug): Response
    {


        $post = $this->postRepository->findOneByPublishedDateAndSlug($date, $slug);

        if (is_null($post)) {
            throw $this->createNotFoundException("Post not found");
        }

        $shareForm = $this->createForm(SharePostFormType::class);

        $shareForm->handleRequest($request);

        if ($shareForm->isSubmitted() && $shareForm->isValid()) {
            $formData = $shareForm->getData();

            $subject = sprintf('%s recommends you to read "%s"', $formData['sender_name'], $post->getTitle());

            $email = (new TemplatedEmail())
                ->from(new Address($this->getParameter('app.contact_email'), $this->getParameter('app.name')))
                ->to($formData['receiver_email'])
                ->subject($subject)
                ->htmlTemplate('emails/posts/share.html.twig')
                ->context([
                    'post' => $post,
                    'sender_name' => $formData['sender_name'],
                    'sender_comments' => $formData['sender_comments'],
                ]);

            $mailer->send($email);

            $this->addFlash('success', '🚀 Post successfully shared.');

            return $this->redirectToRoute('app_home');
        }

        return $this->renderForm('posts/share.html.twig', compact('shareForm', 'post'));
    }
}
