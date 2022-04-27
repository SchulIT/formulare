<?php

namespace App\Controller;

use App\Entity\Submission;
use App\Export\CsvExport;
use App\Form\SettingsType;
use App\Registry\Form;
use App\Registry\FormRegistry;
use App\Seats\AvailableSeatsResolver;
use App\Security\Voter\FormVoter;
use App\Settings\FormSettings;
use App\Submission\SubmissionCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class BackendController extends AbstractController {

    private const NumberOfItems = 25;

    private $registry;
    private $em;

    public function __construct(FormRegistry $registry, EntityManagerInterface $em) {
        $this->registry = $registry;
        $this->em = $em;
    }

    /**
     * @Route("", name="dashboard")
     */
    public function index() {
        return $this->render('admin/index.html.twig', [
            'forms' => $this->registry->getFormsForRoles($this->getUser()->getRoles())
        ]);
    }

    private function getFormOrThrowNotFound(string $alias): Form {
        $form = $this->registry->getForm($alias);

        if($form === null) {
            throw new NotFoundHttpException();
        }

        return $form;
    }

    /**
     * @Route("/{alias}", name="admin_show_form")
     */
    public function show($alias, FormSettings $settings, AvailableSeatsResolver $seatsResolver, SubmissionCalculator $submissionCalculator, Request $request) {
        $form = $this->getFormOrThrowNotFound($alias);
        $this->denyAccessUnlessGranted(FormVoter::Manage, $form);

        $page = $request->query->get('page', 1);
        $query = $this->em
            ->createQueryBuilder()
            ->select('s')
            ->from(Submission::class, 's')
            ->where('s.form = :alias')
            ->setParameter('alias', $form->getAlias())
            ->getQuery()
            ->setMaxResults(static::NumberOfItems)
            ->setFirstResult(($page - 1) * static::NumberOfItems);

        $paginator = new Paginator($query);
        $count = count($paginator);
        $pages = ceil($count / static::NumberOfItems);

        return $this->render('admin/show.html.twig', [
            'form' => $form,
            'submissions' => $paginator,
            'page' => $page,
            'pages' => $pages,
            'count' => $count,
            'settings' => $settings,
            'numberOfSubmissions' => $submissionCalculator->calculateFormSubmissions($form),
            'seatsResolver' => $seatsResolver
        ]);
    }

    /**
     * @Route("/{alias}/settings", name="admin_form_settings")
     */
    public function settings($alias, FormSettings $settings, Request $request) {
        $form = $this->getFormOrThrowNotFound($alias);
        $this->denyAccessUnlessGranted(FormVoter::Manage, $form);

        $settingsForm = $this->createForm(SettingsType::class, [
            'start' => $settings->getFormStartDate($form),
            'end' => $settings->getFormEndDate($form),
            'max_submissions' => $settings->getFormMaxSubmissions($form)
        ]);
        $settingsForm->handleRequest($request);

        if($settingsForm->isSubmitted() && $settingsForm->isValid()) {
            $settings->setFormStartDate($form, $settingsForm->get('start')->getData());
            $settings->setFormEndDate($form, $settingsForm->get('end')->getData());
            $settings->setFormMaxSubmissions($form, $settingsForm->get('max_submissions')->getData());

            $this->addFlash('success', 'admin.settings.success');
            return $this->redirectToRoute('admin_show_form', [
                'alias' => $form->getAlias()
            ]);
        }

        return $this->render('admin/settings.html.twig', [
            'form' => $form,
            'settingsForm' => $settingsForm->createView()
        ]);
    }

    /**
     * @Route("/{alias}/export", name="admin_export_form")
     */
    public function export($alias, CsvExport $csvExport) {
        $form = $this->getFormOrThrowNotFound($alias);
        $this->denyAccessUnlessGranted(FormVoter::Manage, $form);

        return $csvExport->createCsvResponse($form);
    }

    /**
     * @Route("/{alias}/truncate", name="admin_truncate_form")
     */
    public function truncate($alias, Request $request) {
        $form = $this->getFormOrThrowNotFound($alias);
        $this->denyAccessUnlessGranted(FormVoter::Manage, $form);

        $confirm = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.truncate.confirm',
            'message_parameters' => [
                '%name%' => $form->getName()
            ]
        ]);
        $confirm->handleRequest($request);

        if($confirm->isSubmitted() && $confirm->isValid()) {
            $this->em->createQueryBuilder()
                ->delete()
                ->from(Submission::class, 's')
                ->where('s.form = :alias')
                ->setParameter('alias', $form->getAlias())
                ->getQuery()
                ->execute();

            $this->addFlash('success', 'admin.truncate.success');
            return $this->redirectToRoute('admin_show_form', [
                'alias' => $form->getAlias()
            ]);
        }

        return $this->render('admin/truncate.html.twig', [
            'form' => $form,
            'confirm' => $confirm->createView()
        ]);
    }

    /**
     * @Route("/{alias}/{id}/remove", name="admin_remove_record")
     */
    public function removeRecord($alias, $id, Request $request) {
        $form = $this->getFormOrThrowNotFound($alias);
        $this->denyAccessUnlessGranted(FormVoter::Manage, $form);

        $record = $this->em
            ->createQueryBuilder()
            ->select('s')
            ->from(Submission::class, 's')
            ->where('s.id = :id')
            ->andWhere('s.form = :alias')
            ->setParameter('alias', $form->getAlias())
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if($record === null) {
            throw new NotFoundHttpException();
        }

        $confirm = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.remove.confirm',
            'message_parameters' => [
                '%name%' => $form->getName(),
                '%id%' => $id
            ]
        ]);
        $confirm->handleRequest($request);

        if($confirm->isSubmitted() && $confirm->isValid()) {
            $this->em->remove($record);
            $this->em->flush();

            $this->addFlash('success', 'admin.remove.success');
            return $this->redirectToRoute('admin_show_form', [
                'alias' => $form->getAlias()
            ]);
        }

        return $this->render('admin/remove.html.twig', [
            'form' => $form,
            'record' => $record,
            'confirm' => $confirm->createView()
        ]);
    }
}