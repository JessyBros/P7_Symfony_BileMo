<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
    const LIMIT_MAX_BY_PAGE = 5;

    /**
     * @Route("/users", name="users", methods={"GET"})
     */
    public function listUsers(UserRepository $userRepository, UserInterface $customer, Request $request, PaginatorInterface $paginator): Response
    {
        $users = $paginator->paginate(
            $userRepository->findBy(["customer" => $customer]),
            $request->query->getInt('page', 1),
            self::LIMIT_MAX_BY_PAGE
        );
        
        return $this->json($users, Response::HTTP_OK, [], ['groups' => 'list_users']);
    }

    /**
     * @Route("/users/{id<[0-9]+>}", name="user", methods={"GET"})
     * @IsGranted("MANAGE", subject="user", statusCode=403, message="Vous n'avez pas l'autorisation pour consulter les détails de cet utilisateur")
     */
    public function showUser(User $user, UserRepository $userRepository)
    {
        return $this->json($userRepository->findOneBy(["id" => $user]), Response::HTTP_OK, [], ['groups' => 'show_users']);
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
     * @Route("/users/{id<[0-9]+>}", name="delete_user", methods={"DELETE"})
     * @IsGranted("MANAGE", subject="user", statusCode=403, message="Vous n'avez pas l'autorisation pour supprimer cet utilisateur")
     */
    public function deleteUser(User $user, EntityManagerInterface $manager)
    { 
        $manager->remove($user);
        $manager->flush();
        return $this->json(null,Response::HTTP_NO_CONTENT);
    }
}