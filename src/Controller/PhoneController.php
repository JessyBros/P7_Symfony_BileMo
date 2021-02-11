<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Route("/api")
 */
class PhoneController extends AbstractController
{
    /**
     * @Route("/phones", name="phones", methods={"GET"})
     */
    public function listPhones(PhoneRepository $phoneRepository, Request $request): Response
    {
        return $this->json($phoneRepository->findAll(),Response::HTTP_OK);
    }

    /**
     * @Route("/phones/{id}", name="phone", methods={"GET"})
     */
    public function showPhone(Phone $phone)
    {
        return $this->json($phone,Response::HTTP_OK);
    }
}
