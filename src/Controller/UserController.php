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
        return $this->json($userRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'show_users']);
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

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'show_users']);
    }

    /**
     * @Route("/api/add_user", name="add_user", methods={"POST"})
     */
    public function addUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, CustomerRepository $customerRepository, ValidatorInterface $validator)
    {
        $customer = $customerRepository->findOneById(1);
        $jsonPost = $request->getContent();

        try {
            $post = $serializer->deserialize($jsonPost, User::class, 'json');
            $post->setCustomer($customer);

            $errors = $validator->validate($post);
            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $manager->persist($post);
            
            $manager->flush();

            return $this->json($post, 201, [], ['groups' => 'show_users']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                "status" => 400,
                "message" => $e->getMessage()
            ], 400);
        }
    }
}
