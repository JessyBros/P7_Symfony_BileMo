<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Phone;
use App\Repository\PhoneRepository;
use App\Service\KnpPagination;
use App\Service\PhoneSearch;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * @Route("/api")
 */
class PhoneController extends AbstractController
{
    const NUM_PHONES_PER_PAGE = 8;
    const GROUP_JMS_LIST_PHONES = "list_phones";

    /**
     * @Route("/phones", name="phones", methods={"GET"})
     * @OA\Tag(name="Phone")
     * @OA\Get(summary="Retrieves the collection of Phone resources.")
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="The collection page number",
     *     @OA\Schema(type="string", default=1)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Phone collection",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref="#/components/schemas/Phone_list")
     *     )
     * )
     * @OA\Response(response=401, description="Token was expired or not found")
     * @OA\Response(response=404, description="Phone not found")
     * @Security(name="Bearer")
     * @Entity("Phone", expr="repository.findPhonesAll")
     */
    public function listPhones(KnpPagination $knpPagination,
                               PhoneSearch $phoneSearch,
                               Request $request,
                               SerializerInterface $serializer)
    { 
        $defaultPage = $request->query->getInt('page', 1);
        $pathServer = $request->server->get('SERVER_NAME') . $request->getPathInfo() . "?page=";

        $phones = $knpPagination->showPagination(
            $phoneSearch->findAllPhones(),
            self::NUM_PHONES_PER_PAGE,
            self::GROUP_JMS_LIST_PHONES,
            $defaultPage,
            $pathServer
        );

        $phones = $serializer->serialize($phones, 'json');
        
        $response =  new JsonResponse($phones, Response::HTTP_OK, [], true);
        $response->setPublic()->setMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/phones/{id<[0-9]+>}", name="phone", methods={"GET"})
     * @OA\Tag(name="Phone")
     * @OA\Get(summary="Retrieves a Phone resource.")
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Resource identifier",
     *     allowEmptyValue="1",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Phone resource",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref="#/components/schemas/Phone")
     *     )
     * )
     * @OA\Response(response=401, description="Token was expired or not found")
     * @OA\Response(response=404, description="Phone not found")
     * @Security(name="Bearer")
     */
    public function showPhone($id, PhoneSearch $phoneSearch, SerializerInterface $serializer)
    {
        $phone = $serializer->serialize($phoneSearch->findPhoneById($id), 'json', SerializationContext::create()->setGroups(array('show_phones')));

        $response =  new JsonResponse($phone, Response::HTTP_OK, [], true);
        $response->setPublic()->setMaxAge(3600);
        return $response;
    }
}