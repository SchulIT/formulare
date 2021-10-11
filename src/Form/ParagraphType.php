<?php

namespace App\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParagraphType extends AbstractFormType {
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('paragraphs');
        $resolver->setAllowedTypes('paragraphs', 'string[]');
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['paragraphs'] = $options['paragraphs'];
    }

    public function getBlockPrefix() {
        return 'paragraph';
    }
}