<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractFormType extends AbstractType {
    public function configureOptions(OptionsResolver $resolver) {
        $resolver
            ->setRequired('items');
    }
}