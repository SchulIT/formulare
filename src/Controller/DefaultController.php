<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {

    public function __construct(private readonly string $redirectUrl)
    {
    }

    #[Route(path: '')]
    #[Route(path: '/')]
    public function index(): RedirectResponse {
        return $this->redirect($this->redirectUrl);
    }
}