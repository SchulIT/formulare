<?php

namespace App\Security\Voter;

use App\Registry\Form;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FormVoter extends Voter {

    public const Manage = 'manage';

    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager) {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    protected function supports(string $attribute, $subject) {
        return $attribute === static::Manage
            && $subject instanceof Form;
    }

    /**
     * @param string $attribute
     * @param Form $subject
     * @param TokenInterface $token
     * @return bool|void
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token) {
        return $this->accessDecisionManager->decide($token, [ $subject->getAdminRole() ]);
    }
}