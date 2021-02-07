<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{
    /**
     * @Route("/api/phones", name="phones", methods={"GET"})
     */
    public function listPhones(PhoneRepository $phoneRepository): Response
    {
        return $this->json($phoneRepository->findAll(),Response::HTTP_OK);
    }

    /**
     * @Route("/api/phones/{id}", name="phone", methods={"GET"})
     */
    public function showPhone(Phone $phone)
    {
        return $this->json($phone,Response::HTTP_OK);
    }
}
