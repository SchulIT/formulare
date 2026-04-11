<?php

namespace App\Autoconfig;

use App\Registry\FormRegistry;
use Override;
use SchulIT\CommonBundle\Autoconfig\Roles\RoleResolverInterface;

readonly class RoleResolver implements RoleResolverInterface {

    public function __construct(
        private FormRegistry $formRegistry,
    ) {

    }

    #[Override]
    public function resolve(): array {
        $roles = ['ROLE_USER'];

        foreach($this->formRegistry->getForms() as $form) {
            $roles[] = $form->getAdminRole();
        }

        return $roles;
    }
}