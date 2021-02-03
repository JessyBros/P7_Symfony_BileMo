<?php

namespace App\Controller;

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
        return $this->json($userRepository->findAll(), 200, [], ['groups' => 'list_users']);
    }

    /**
     * @Route("/api/user/{id}", name="user", methods={"GET"})
     */
    public function showUser(int $id, UserRepository $userRepository)
    {
        $user = $userRepository->findOneById($id);

        if ($user == null) {
            throw $this->createNotFoundException("L'utilisateur " . $id ." n'a pas été trouvé !");
        }

        return $this->json($user, 200, [], ['groups' => 'list_users']);
    }
}
