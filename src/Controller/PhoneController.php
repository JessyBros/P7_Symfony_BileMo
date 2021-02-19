<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Phone;
use App\Repository\PhoneRepository;
use App\Service\ShowPagination;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Serializer\Encoder\JsonEncode;


/**
 * @Route("/api")
 */
class PhoneController extends AbstractController
{
    const LIMIT_MAX_BY_PAGE = 10;

    /**
     * @Route("/phones", name="phones", methods={"GET"})
     */
    public function listPhones(PhoneRepository $phoneRepository, Request $request, PaginatorInterface $paginator, SerializerInterface $serializer, ShowPagination $pagination)
    {
        $phones = $paginator->paginate(
            $phoneRepository->findAll(),
            $request->query->getInt('page', 1),
            self::LIMIT_MAX_BY_PAGE,[],   
        );

        $route = $request->server->get('SERVER_NAME') . $request->getPathInfo() . "?page=";
        $page = $phones->getCurrentPageNumber(); 
        $totalPhoneCount = $phones->getTotalItemCount();
        $maxPage = ceil($totalPhoneCount/ self::LIMIT_MAX_BY_PAGE);

        $pagination = $pagination->showPagination($route, $page, $totalPhoneCount, $maxPage);
        $phones[] = ["Pagination" => $pagination];


        $phones = $serializer->serialize($phones, 'json', SerializationContext::create()->setGroups(array('Default', 'items' => array('list_phones'))));
        

        
        $response =  new JsonResponse($phones, Response::HTTP_OK, [], true);

        $response->setPublic();
        $response->setMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/phones/{id<[0-9]+>}", name="phone", methods={"GET"})
     */
    public function showPhone(Phone $phone, SerializerInterface $serializer)
    {
        $phone = $serializer->serialize($phone, 'json', SerializationContext::create()->setGroups(array('show_phones')));
        $response =  new JsonResponse($phone, Response::HTTP_OK, [], true);
        $response->setPublic();
        $response->setMaxAge(3600);
        return $response;
    }
}
