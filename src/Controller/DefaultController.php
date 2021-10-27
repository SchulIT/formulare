<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {

    private $redirectUrl;

    public function __construct(string $redirectUrl) {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * @Route("")
     * @Route("/")
     */
    public function index(): RedirectResponse {
        return $this->redirect($this->redirectUrl);
    }
}