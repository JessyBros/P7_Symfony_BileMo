<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{
    /**
     * @Route("/api/phones", name="phones", methods={"GET"})
     */
    public function index(PhoneRepository $phoneRepository)
    {
        return $this->json($phoneRepository->findAll(),200, []);
    }
}
