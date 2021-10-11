<?php

namespace App\Security\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class PasswordRequestMatcher implements RequestMatcherInterface {

    public const RouteName = 'authenticate_form';

    /**
     * @inheritDoc
     */
    public function matches(Request $request) {
        return $request->attributes->get('_route') === self::RouteName;
    }
}