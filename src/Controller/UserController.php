<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use App\Service\ShowPagination;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
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
    public function listUsers(UserRepository $userRepository,
                              UserInterface $customer,
                              Request $request,
                              PaginatorInterface $paginator,
                              SerializerInterface $serializer,
                              ShowPagination $pagination)
    {
        $users = $paginator->paginate(
            $userRepository->findBy(["customer" => $customer]),
            $request->query->getInt('page', 1),
            self::LIMIT_MAX_BY_PAGE
        );

        // Make Link pagination
        $route = $request->server->get('SERVER_NAME') . $request->getPathInfo() . "?page=";
        $page = $users->getCurrentPageNumber(); 
        $totalPhoneCount = $users->getTotalItemCount();
        $maxPage = ceil($totalPhoneCount / self::LIMIT_MAX_BY_PAGE);

        $pagination = $pagination->showPagination($route, $page, $totalPhoneCount, $maxPage);
        $users[] = ["Pagination" => $pagination];

        $users = $serializer->serialize($users, 'json', SerializationContext::create()->setGroups(array('Default', 'items' => array('list_users'))));
        return  new JsonResponse($users, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/users/{id<[0-9]+>}", name="user", methods={"GET"})
     * @IsGranted("MANAGE", subject="user", statusCode=403, message="Vous n'avez pas l'autorisation pour consulter les détails de cet utilisateur")
     */
    public function showUser(User $user, UserRepository $userRepository, SerializerInterface $serializer)
    {
        $user = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups('show_users'));
        return new JsonResponse($user, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/users", name="add_user", methods={"POST"})
     */
    public function addUser(Request $request,
                            SerializerInterface $serializer,
                            EntityManagerInterface $manager,
                            CustomerRepository $customerRepository,
                            ValidatorInterface $validator,
                            UserInterface $customer)
    {
        $jsonPost = $request->getContent();

        try {
            $user = $serializer->deserialize($jsonPost, User::class, 'json');
            $user->setCustomer($customer);

            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                $errors = $serializer->serialize($errors, 'json');
                return new JsonResponse($errors, Response::HTTP_BAD_REQUEST, [], true);
            }

            $manager->persist($user);
            $manager->flush();
            $user = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups('add_user'));
            return new JsonResponse($user, Response::HTTP_CREATED, [], true);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
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
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}