<?php

namespace App\Submission;

use App\Entity\Submission;
use App\Registry\Form;
use Doctrine\ORM\EntityManagerInterface;

class SubmissionCalculator {
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function calculateFormSubmissions(Form $form): int {
        $count = (int)$this->em
            ->createQueryBuilder()
            ->select('COUNT(1)')
            ->from(Submission::class, 's')
            ->where('s.form = :alias')
            ->setParameter('alias', $form->getAlias())
            ->getQuery()
            ->getSingleScalarResult();

        if(($property = $form->getCountableProperty()) !== null) {
            $count += (int)$this->em->createQueryBuilder()
                ->select(sprintf('SUM(JSON_LENGTH(s.data, \'$.%s\'))', $property))
                ->from(Submission::class, 's')
                ->where('s.form = :alias')
                ->setParameter('alias', $form->getAlias())
                ->getQuery()
                ->getSingleScalarResult();
        }

        return $count;
    }
}