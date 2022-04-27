<?php

namespace App\Controller;

use App\Entity\Submission;
use App\Registry\Form;
use App\Registry\FormRegistry;
use App\Seats\AvailableSeatsResolver;
use App\Settings\FormSettings;
use App\Submission\SubmissionCalculator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/{formAlias}")
 */
class FrontendController extends AbstractController {

    private $registry;

    public function __construct(FormRegistry $registry) {
        $this->registry = $registry;
    }

    private function getFormOrThrowNotFound(string $formAlias): Form {
        if($this->registry->hasForm($formAlias)) {
            return $this->registry->getForm($formAlias);
        }

        throw new NotFoundHttpException();
    }

    /**
     * @Route("/login", name="authenticate_form")
     */
    public function password(string $formAlias, AuthenticationUtils $authenticationUtils) {
        $formOptions = $this->getFormOrThrowNotFound($formAlias);

        return $this->render('frontend/login.html.twig', [
            'username' => $formAlias,
            'formOptions' => $formOptions,
            'formAlias' => $formAlias,
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/login/check", name="check_authenticate_form", methods={"POST"})
     */
    public function passwordCheck(string $formAlias) {
        throw new \LogicException('This code should not be executed.');
    }

    /**
     * @Route("/logout", name="form_logout")
     */
    public function logout(string $formAlias) {
        return $this->redirectToRoute('form_success', [
            'formAlias' => $formAlias
        ]);
    }

    /**
     * @Route("/success", name="form_success")
     */
    public function success(string $formAlias) {
        $formOptions = $this->getFormOrThrowNotFound($formAlias);
        return $this->render('frontend/success.html.twig', [
            'formOptions' => $formOptions,
            'formAlias' => $formAlias
        ]);
    }

    /**
     * @Route("", name="show_form")
     */
    public function form(string $formAlias, Request $request, EntityManagerInterface $manager,
                         AvailableSeatsResolver $seatsResolver, FormSettings $settings,
                         SubmissionCalculator $submissionCalculator, PropertyAccessorInterface $propertyAccessor) {
        $formModel = $this->getFormOrThrowNotFound($formAlias);
        $now = new DateTime('now');

        if($settings->getFormStartDate($formModel) !== null && $now < $settings->getFormStartDate($formModel)) {
            return $this->render('frontend/too_early.html.twig', [
                'form' => $formModel,
                'start' => $settings->getFormStartDate($formModel)
            ]);
        }

        if($settings->getFormEndDate($formModel) !== null && $now > $settings->getFormEndDate($formModel)) {
            return $this->render('frontend/too_late.html.twig', [
                'form' => $formModel
            ]);
        }

        if(($max = $settings->getFormMaxSubmissions($formModel)) !== null) {
            $count = $submissionCalculator->calculateFormSubmissions($formModel);

            if($count >= $max) {
                return $this->render('frontend/maximum_reached.html.twig', [
                    'form' => $formModel
                ]);
            }
        }

        if($formModel->hasSeats()) {
            foreach($formModel->getItems() as $alias => $item) {
                if(isset($item['seats'])) {
                    $info = $seatsResolver->resolveSeats($formModel, $alias);

                    if($info->hasAvailableSeats() === false) {
                        return $this->render('frontend/maximum_reached.html.twig', [
                            'form' => $formModel
                        ]);
                    }
                }
            }
        }

        $data = [];

        $options = [
            'items' => $formModel->getItems()
        ];

        $form = $this->createForm($formModel->getFormClass(), $data, $options);
        $form->handleRequest($request);

        if($form->isSubmitted()) {


            if($form->isValid()) {

                $submission = (new Submission())
                    ->setForm($formModel->getAlias())
                    ->setData($form->getData());

                $manager->persist($submission);
                $manager->flush();

                return $this->redirectToRoute('form_logout', [
                    'formAlias' => $formAlias
                ]);
            }
        }

        return $this->render('frontend/form.html.twig', [
            'form' => $form->createView(),
            'formOptions' => $formModel,
            'end' => $settings->getFormEndDate($formModel)
        ]);
    }
}