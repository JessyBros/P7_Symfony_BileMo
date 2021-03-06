<?php

namespace App\Service;

use App\Repository\PhoneRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class PhoneSearch
{
    private PhoneRepository $phoneRepository;

    public function __construct(PhoneRepository $phoneRepository)
    {
        $this->phoneRepository = $phoneRepository;
    }

    public function findAllPhones()
    {
        $phone = $this->phoneRepository->findAll();

        if (!$phone) {
            throw new Exception("Aucun téléphone n'existe", Response::HTTP_NOT_FOUND);
        }

        return $phone;
    }

    public function findPhoneById($id)
    {
        $phone = $this->phoneRepository->findOneById(['id' => $id]);
        if (!$phone) {
            throw new Exception("Le téléphone que vous recherchez n'existe pas", Response::HTTP_NOT_FOUND);
        }

        return $phone;
    }
}
