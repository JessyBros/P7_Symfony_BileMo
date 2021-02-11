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
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/users", name="users", methods={"GET"})
     */
    public function listUsers(UserRepository $userRepository, UserInterface $customer): Response
    {
        return $this->json($userRepository->findBy(["customer" => $customer]), Response::HTTP_OK, [], ['groups' => 'show_users']);
    }

    /**
     * @Route("/users/{id<[0-9]+>}", name="user", methods={"GET"})
     */
    public function showUser(User $user, UserRepository $userRepository, UserInterface $customer)
    {
        return $this->json($userRepository->findOneBy(["id" => $user,"customer" => $customer]), Response::HTTP_OK, [], ['groups' => 'show_users']);
    }

    /**
     * @Route("/users", name="add_user", methods={"POST"})
     */
    public function addUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, CustomerRepository $customerRepository, ValidatorInterface $validator, UserInterface $customer)
    {
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
            ], );
        }
    }

    /**
     * @Route("/users/{id}", name="delete_user", methods={"DELETE"})
     */
    public function deleteUser(User $user, UserRepository $userRepository, UserInterface $customer, EntityManagerInterface $manager)
    {     
        $userFromCustomer = $userRepository->findOneBy(['id' => $user, 'customer' => $customer]);

        if (!$userFromCustomer) {
            return $this->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => "Erreur lors de la suppresion de l'utilisateur"
            ], Response::HTTP_BAD_REQUEST);
        }

        $manager->remove($user);
        $manager->flush();
        return $this->json($user, Response::HTTP_NO_CONTENT, [], ['groups' => 'show_users']);
    }
}