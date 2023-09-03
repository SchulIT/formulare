<?php

namespace App\Security\Frontend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutHandler implements LogoutSuccessHandlerInterface {

    private const SuccessRoute = 'form_success';

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * @inheritDoc
     */
    public function onLogoutSuccess(Request $request) {
        $formAlias = $this->getFormAlias($request);

        if($formAlias === null) {
            return new RedirectResponse('/');
        }

        return new RedirectResponse(
            $this->urlGenerator->generate(static::SuccessRoute, [
                'formAlias' => $formAlias
            ])
        );
    }

    private function getFormAlias(Request $request) {
        $routeParams = $request->attributes->get('_route_params', [ ]);
        return $routeParams['formAlias'] ?? null;
    }
}