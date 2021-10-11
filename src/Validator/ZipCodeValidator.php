<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ZipCodeValidator extends ConstraintValidator {

    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof ZipCode) {
            throw new UnexpectedTypeException($constraint, ZipCode::class);
        }

        if(null === $value || '' === $value) {
            return;
        }

        if(!preg_match('~^[0-9]{5}$~', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->addViolation();
        }
    }
}