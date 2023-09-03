<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ZipCode extends Constraint {
    public string $message = '{{ code }} is not a valid ZIP code.';
}