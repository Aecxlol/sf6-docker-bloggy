<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DemoController extends AbstractController
{
    #[Route('/demo', name: 'app_demo')]
    public function index(UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = new User();
        $user->setName('azezaezaeae')
            ->setEmail('azeaeza@azeaeaze.fr')
            ->setPassword('fohzeiohokreopzkropzkr');

        $userRepository->add($user, flush: true);


        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DemoController.php',
        ]);
    }
}
