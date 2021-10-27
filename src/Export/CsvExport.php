<?php

namespace App\Export;

use App\Entity\Submission;
use App\Registry\Form;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CsvExport {
    private const Separator = ';';

    private $em;
    private $translator;
    private $propertyAccessor;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, PropertyAccessorInterface $propertyAccessor) {
        $this->em = $em;
        $this->translator = $translator;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function createCsv(Form $form): string {
        /** @var Submission[] $submissions */
        $submissions = $this->em
            ->createQueryBuilder()
            ->select('s')
            ->from(Submission::class, 's')
            ->where('s.form = :alias')
            ->setParameter('alias', $form->getAlias())
            ->orderBy('s.date', 'asc')
            ->getQuery()
            ->getResult();

        $writer = Writer::createFromString();
        $writer->setOutputBOM(Writer::BOM_UTF8);
        $writer->setDelimiter(static::Separator);

        $header = [ ];
        foreach($form->getItems() as $item) {
            $header[] = $item['label'];

            if(isset($item['add'])) {
                $header[] = $this->translator->trans('label.number', ['%label%' => $item['label']]);
            }
        }

        $header[] = $this->translator->trans('label.timestamp');
        $writer->insertOne($header);

        foreach($submissions as $submission) {
            $row = [];

            foreach($form->getItems() as $key => $item) {
                if(isset($item['add'])) {
                    $collection = $this->propertyAccessor->getValue($submission->getData(), '[' . $key . ']');
                    $row[] = implode(', ', $collection);
                    $row[] = count($collection);
                } else {
                    $row[] = $this->propertyAccessor->getValue($submission->getData(), '[' . $key . ']');
                }
            }

            $row[] = $submission->getDate()->format('Y-m-d H:i:s');
            $writer->insertOne($row);
        }

        return $writer->toString();
    }

    public function createCsvResponse(Form $form): Response {
        $csv = $this->createCsv($form);

        $response = new Response($csv);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, transliterator_transliterate('Latin-ASCII', $form->getAlias()) . '.csv'));

        return $response;
    }
}