<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class Seats extends Constraint {
    public $form = null;

    public string $message = 'Invalid option. All seats are taken.';
}