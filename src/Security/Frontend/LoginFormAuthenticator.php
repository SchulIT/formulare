<?php

namespace App\Security\Frontend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface {
    use TargetPathTrait;

    private const FallbackRoute = 'index';
    private const LoginRoute = 'authenticate_form';
    private const CheckRoute = 'check_authenticate_form';
    private const FormRoute = 'show_form';
    private $passwordEncoder;

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator, private readonly RequestStack $requestStack, private readonly CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supportsRememberMe() {
        return false;
    }

    private function getFormAlias(Request $request) {
        $routeParams = $request->attributes->get('_route_params', [ ]);
        return $routeParams['formAlias'] ?? null;
    }

    /**
     * @inheritDoc
     */
    protected function getLoginUrl() {
        $formAlias = $this->getFormAlias($this->requestStack->getMasterRequest());

        if($formAlias !== null) {
            return $this->urlGenerator->generate(static::LoginRoute, [
                'formAlias' => $formAlias
            ]);
        }

        return $this->urlGenerator->generate(static::FallbackRoute);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request) {
        return $request->attributes->get('_route') === static::CheckRoute
            && $request->isMethod('POST');
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request) {
        return [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
            'csrf_token' => $request->request->get('_csrf_token')
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider) {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if(!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $userProvider->loadUserByIdentifier($credentials['username']);

        if($user === null) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user) {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        if($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        $formAlias = $this->getFormAlias($request);
        return new RedirectResponse(
            $this->urlGenerator->generate(static::FormRoute, [
                'formAlias' => $formAlias
            ])
        );
    }

    /**
     * @inheritDoc
     */
    public function getPassword($credentials): ?string {
        return $credentials['password'] ?? null;
    }
}