<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use App\Service\KnpPagination;
use App\Service\UserSearch;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
    const NUM_USERS_PER_PAGE = 5;
    const GROUP_JMS_LIST_USERS = "list_users";

    /**
     * @Route("/users", name="users", methods={"GET"})
     * @OA\Tag(name="User")
     * @OA\Get(summary="Retrieves the collection of User resources.")
      * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="The collection page number",
     *     @OA\Schema(type="string", default=1)
     * )
     * @OA\Response(
     *     response=200,
     *     description="User collection",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref="#/components/schemas/User_list")
     *     )
     * )
     * @OA\Response(response=401, description="Token was expired or not found")
     * @OA\Response(response=404, description="User not found")
     * @Security(name="Bearer")
     */
    public function listUsers(KnpPagination $knpPagination,
                              Request $request,
                              SerializerInterface $serializer,
                              UserSearch $userSearch,
                              UserInterface $customer)
    {
        $pathServer = $request->server->get('SERVER_NAME') . $request->getPathInfo() . "?page=";
        $defaultPage = $request->query->getInt('page', 1);

        $users = $knpPagination->showPagination(
            $userSearch->findAllUsersBy(["customer" => $customer]),
            self::NUM_USERS_PER_PAGE,
            self::GROUP_JMS_LIST_USERS,
            $defaultPage,
            $pathServer
        );

        $users = $serializer->serialize($users, 'json');
        return  new JsonResponse($users, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/users/{id<[0-9]+>}", name="user", methods={"GET"})
     * @IsGranted("MANAGE", subject="id", statusCode=403, message="Vous n'avez pas l'autorisation pour consulter les détails de cet utilisateur")
     * @OA\Tag(name="User")
     * @OA\Get(summary="Retrieves a User resource.")
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Resource identifier",
     *     allowEmptyValue="1",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=200,
     *     description="User resource",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref="#/components/schemas/User")
     *     )
     * )
     * @OA\Response(response=401, description="Token was expired or not found")
     * @OA\Response(response=404, description="User not found")
     * @Security(name="Bearer")
     */
    public function showUser($id, SerializerInterface $serializer, UserSearch $userSearch)
    {
        $user = $serializer->serialize($userSearch->findUserById($id), 'json', SerializationContext::create()->setGroups('show_users'));
        return new JsonResponse($user, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/users", name="add_user", methods={"POST"})
     * @OA\Tag(name="User")
     * @OA\Post(summary="Creates a User resource.")
     * @OA\RequestBody(
     *         description="The new User resource",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/Id+json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="number",
     *                     type="string"
     *                 ),
     *             )
     *         ))
     * @OA\Response(
     *     response=204,
     *     description="user resource created",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"show_users"}))
     *     )
     * )
     * @OA\Response(response=400, description="Invalid input")
     * @OA\Response(response=401, description="Token was expired or not found")
     * @Security(name="Bearer")
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
        } catch (NotEncodableValueException $e) {
            return $this->json([
                "status" => Response::HTTP_BAD_REQUEST,
                "message" => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

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
    }

    /**
     * @Route("/users/{id<[0-9]+>}", name="delete_user", methods={"DELETE"})
     * @IsGranted("MANAGE", subject="id", statusCode=403, message="Vous n'avez pas l'autorisation pour supprimer cet utilisateur")
     * @OA\Tag(name="User")
     * @OA\Delete(summary="Removes the User resource.")
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Resource identifier",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *     response=204,
     *     description="user deleted"
     * )
     * @OA\Response(
     *     response=401,
     *     description="Token was expired or not found",
     * )
     * @OA\Response(
     *     response=404,
     *     description="No user was found",
     * )
     * @Security(name="Bearer")
     */
    public function deleteUser($id, EntityManagerInterface $manager, UserSearch $userSearch)
    { 
        $user = $userSearch->findUserById($id);

        $manager->remove($user);
        $manager->flush();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}