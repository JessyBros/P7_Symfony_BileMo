<?php

namespace App\Service;

use App\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserSearch{
    
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function findAllUsersBy($customer) 
    {
        $user = $this->userRepository->findBy($customer);

        if (!$user) {
            throw new Exception("Aucun utilisateur n'existe", Response::HTTP_NOT_FOUND);
        }

        return $user;       
    }

    public function findUserById($id) 
    {
        $user = $this->userRepository->findOneById(["id"=>$id],);
        if (!$user) {
            throw new Exception("L'utilisateur que vous recherchez n'existe pas", Response::HTTP_NOT_FOUND);
        }

        return $user;   
    }

}