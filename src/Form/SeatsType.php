<?php

namespace App\Form;

use App\Registry\FormRegistry;
use App\Seats\AvailableSeatsResolver;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeatsType extends ChoiceType {

    private $registry;
    private $seatsResolver;

    public function __construct(FormRegistry $registry, AvailableSeatsResolver $seatsResolver, ChoiceListFactoryInterface $choiceListFactory = null, $translator = null) {
        parent::__construct($choiceListFactory, $translator);

        $this->registry = $registry;
        $this->seatsResolver = $seatsResolver;
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('form')
            ->setRequired('seats');
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        parent::buildView($view, $form, $options);

        $view->vars['seats_info'] = $this->seatsResolver->resolveSeats(
            $this->registry->getForm($options['form']),
            $view->vars['name']
        );
    }

    public function getBlockPrefix() {
        return 'seats';
    }
}