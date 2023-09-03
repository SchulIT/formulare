<?php

namespace App\Security\Voter;

use App\Registry\Form;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FormVoter extends Voter {

    final public const Manage = 'manage';

    public function __construct(private readonly AccessDecisionManagerInterface $accessDecisionManager) { }

    protected function supports(string $attribute, $subject): bool {
        return $attribute === static::Manage
            && $subject instanceof Form;
    }

    /**
     * @param string $attribute
     * @param Form $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool {
        return $this->accessDecisionManager->decide($token, [ $subject->getAdminRole() ]);
    }
}