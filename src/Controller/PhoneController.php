<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Phone;
use App\Repository\PhoneRepository;
use App\Service\KnpPagination;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * @Route("/api")
 */
class PhoneController extends AbstractController
{
    const NUM_PHONES_PER_PAGE = 8;
    const GROUP_JMS_LIST_PHONES = "list_phones";

    /**
     * @Route("/phones", name="phones", methods={"GET"})
     */
    public function listPhones(KnpPagination $knpPagination,
                               PhoneRepository $phoneRepository,
                               Request $request,
                               SerializerInterface $serializer)
    { 
        $defaultPage = $request->query->getInt('page', 1);
        $pathServer = $request->server->get('SERVER_NAME') . $request->getPathInfo() . "?page=";

        $phones = $knpPagination->showPagination(
            $phoneRepository->findAll(),
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
     */
    public function showPhone(Phone $phone, SerializerInterface $serializer)
    {
        $phone = $serializer->serialize($phone, 'json', SerializationContext::create()->setGroups(array('show_phones')));

        $response =  new JsonResponse($phone, Response::HTTP_OK, [], true);
        $response->setPublic()->setMaxAge(3600);
        return $response;
    }
}
