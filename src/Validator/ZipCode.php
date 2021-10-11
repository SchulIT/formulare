<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ZipCode extends Constraint {
    public $message = '{{ code }} is not a valid ZIP code.';
}