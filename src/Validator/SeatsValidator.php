<?php

namespace App\Validator;

use App\Registry\FormRegistry;
use App\Seats\AvailableSeatsResolver;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Mapping\PropertyMetadata;

class SeatsValidator extends ConstraintValidator {

    public function __construct(private readonly AvailableSeatsResolver $seatsResolver, private readonly FormRegistry $registry) { }

    /**
     * @throws Exception
     */
    public function validate($value, Constraint $constraint): void {
        if(!$constraint instanceof Seats) {
            throw new UnexpectedTypeException($constraint, Seats::class);
        }

        if(null === $value || '' === $value) {
            return;
        }

        if($constraint->form === null) {
            throw new Exception('You must specify the `form` option of the Seats constraint.');
        }

        $form = $this->registry->getForm($constraint->form);

        $metadata = $this->context->getMetadata();

        if(!$metadata instanceof PropertyMetadata) {
            return;
        }

        $info = $this->seatsResolver->resolveSeats($form, $metadata->getPropertyName());

        if($info->getAvailable($value) <= 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}