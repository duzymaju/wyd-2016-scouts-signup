<?php

namespace Wyd2016Bundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validator Constraints
 *
 * @Annotation
 */
class ContainsPesel extends Constraint
{
    /** @var string */
    public $message = 'This is not a valid PESEL number.';
}
