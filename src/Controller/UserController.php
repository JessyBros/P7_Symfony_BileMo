<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="users")
     */
    public function listUsers(UserRepository $userRepository): Response
    {
        return $this->json($userRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'show_users']);
    }

    /**
     * @Route("/api/users/{id<[0-9]+>}", name="user", methods={"GET"})
     */
    public function showUser(User $user)
    {
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'show_users']);
    }
}
