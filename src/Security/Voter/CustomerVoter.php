<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomerVoter extends Voter
{
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['MANAGE'])
            && $subject;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $subject */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $subject = $this->userRepository->findOneById($subject);
        if (!$subject) {
            return true;
        }

        switch ($attribute) {
            case 'MANAGE':
                if ($subject->getCustomer() == $user) {
                    return true;
                }
                break;
        }

        return false;
    }
}
