<?php

namespace App\Controller;

use App\Settings\AppSettings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class AppSettingsController extends AbstractController {

    #[Route('/admin/settings', name: 'app_settings')]
    public function __invoke(
        Request $request,
        AppSettings $appSettings
    ): Response {
        $form = $this->createFormBuilder([
            'customCss' => $appSettings->getCustomCss()
        ])
            ->add('customCss', TextareaType::class, [
                'label' => 'settings.app.custom_css.label',
                'help' => 'settings.app.custom_css.help',
                'attr' => [
                    'rows' => 30,
                    'class' => 'font-monospace'
                ]
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appSettings->setCustomCss($form->getData()['customCss']);

            $this->addFlash('success', 'settings.success');
            return $this->redirectToRoute('app_settings');
        }

        return $this->render('admin/settings/app.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}