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
    public function listPhones(PhoneRepository $phoneRepository): Response
    {
        return $this->json($phoneRepository->findAll(),200);
    }

    /**
     * @Route("/api/phone/{id}", name="phone", methods={"GET"})
     */
    public function showPhone(int $id, PhoneRepository $phoneRepository)
    {
        $phone = $phoneRepository->findOneById($id);

        if ($phone == null) {
            throw $this->createNotFoundException("Le téléphone " . $id ." n'a pas été trouvé !");
        }

        return $this->json($phone,200);
    }
}
