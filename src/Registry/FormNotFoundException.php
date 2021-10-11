<?php

namespace App\Registry;

use Throwable;

class FormNotFoundException extends \Exception {
    public function __construct(string $alias, $code = 0, Throwable $previous = null) {
        parent::__construct(sprintf('Form with alias "%s" was not found.', $alias), $code, $previous);
    }
}