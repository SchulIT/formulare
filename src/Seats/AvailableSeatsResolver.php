<?php

namespace App\Seats;

use App\Entity\Submission;
use App\Registry\Form;
use Doctrine\ORM\EntityManagerInterface;

class AvailableSeatsResolver {
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function resolveSeats(Form $form, string $property): SeatsInformation {
        $choices = array_keys($form->getItems()[$property]['choices']);

        $info = new SeatsInformation($property, $choices);

        foreach($form->getItems()[$property]['seats'] as $choice => $seats) {
            $info->setTotal($choice, $seats);
        }

        $rows = $this->em->createQueryBuilder()
            ->select(sprintf('JSON_VALUE(s.data, \'$.%s\')', $property))
            ->from(Submission::class, 's')
            ->where('s.form = :alias')
            ->setParameter('alias', $form->getAlias())
            ->getQuery()
            ->getArrayResult();

        foreach($rows as $row) {
            $info->decreaseAvailable($row[1]);
        }

        return $info;
    }
}