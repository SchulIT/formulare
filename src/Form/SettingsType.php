<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('start', DateTimeType::class, [
                'label' => 'admin.settings.start.label',
                'help' => 'admin.settings.start.help',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false
            ])
            ->add('end', DateTimeType::class, [
                'label' => 'admin.settings.end.label',
                'help' => 'admin.settings.end.help',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false
            ])
            ->add('max_submissions', IntegerType::class, [
                'label' => 'admin.settings.max.label',
                'help' => 'admin.settings.max.help',
                'required' => false
            ]);
    }
}