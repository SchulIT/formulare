<?php

namespace App\Mariadb;

use Scienta\DoctrineJsonFunctions\Query\AST\Functions\Mariadb\MariadbJsonFunctionNode;

/**
 * "JSON_LENGTH" "(" StringPrimary "," StringPrimary ")"
 */
class JsonLength extends MariadbJsonFunctionNode {
    final public const FUNCTION_NAME = 'JSON_LENGTH';

    /** @var string[] */
    protected $requiredArgumentTypes = [self::STRING_PRIMARY_ARG, self::STRING_PRIMARY_ARG];
}