<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="users")
     */
    public function listUsers(UserRepository $userRepository): Response
    {
        return $this->json($userRepository->findAll(), 200, [], ['groups' => 'show_users']);
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

        return $this->json($user, 200, [], ['groups' => 'show_users']);
    }

    /**
     * @Route("/api/add_user", name="add_user", methods={"POST"})
     */
    public function addUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, CustomerRepository $customerRepository, ValidatorInterface $validator)
    {
        $customer = $customerRepository->findOneById(1);
        $jsonPost = $request->getContent();

        try {
            $user = $serializer->deserialize($jsonPost, User::class, 'json');
            $user->setCustomer($customer);

            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $manager->persist($user);
            
            $manager->flush();

            return $this->json($user, 201, [], ['groups' => 'show_users']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                "status" => 400,
                "message" => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route("/api/delete_user/{id}", name="delete_user", methods={"DELETE"})
     */
    public function deleteUser($id, EntityManagerInterface $manager, UserRepository $userRepository)
    {
        $user = $userRepository->findOneById($id);

        if ($user == null ) {
            throw $this->createNotFoundException("L'utilisateur " . $id ." n'a pas été trouvé !");
        }

        $manager->remove($user);
        $manager->flush();
        return $this->json($user, 204, [], ['groups' => 'show_users']);
    }
}
