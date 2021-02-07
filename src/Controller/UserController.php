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
     * @Route("/api/users", name="users", methods={"GET"})
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

    /**
     * @Route("/api/users", name="add_user", methods={"POST"})
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
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }

            $manager->persist($user);
            $manager->flush();
            return $this->json($user, Response::HTTP_CREATED, [], ['groups' => 'show_users']);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
