<?php

namespace App\Security\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class PasswordRequestMatcher implements RequestMatcherInterface {

    final public const RouteName = 'authenticate_form';

    /**
     * @inheritDoc
     */
    public function matches(Request $request): bool {
        return $request->attributes->get('_route') === self::RouteName;
    }
}