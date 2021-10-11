<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Seats extends Constraint {
    public $form = null;

    public $message = 'Invalid option. All seats are taken.';
}