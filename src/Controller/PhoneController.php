<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Phone;
use App\Repository\PhoneRepository;
use ContainerPuP5l9S\PaginatorInterface_82dac15;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\SecurityContext;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/api")
 */
class PhoneController extends AbstractController
{
    const LIMIT_MAX_BY_PAGE = 10;

    /**
     * @Route("/phones", name="phones", methods={"GET"})
     */
    public function listPhones(PhoneRepository $phoneRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $phones = $paginator->paginate(
            $phoneRepository->findAll(),
            $request->query->getInt('page', 1),
            self::LIMIT_MAX_BY_PAGE
        );

        return $this->json($phones, Response::HTTP_OK, [], ['groups' => 'list_phones']);
    }

    /**
     * @Route("/phones/{id}", name="phone", methods={"GET"})
     */
    public function showPhone(Phone $phone)
    {
        return $this->json($phone, Response::HTTP_OK, [], ['groups' => 'show_phones']);
    }
}
