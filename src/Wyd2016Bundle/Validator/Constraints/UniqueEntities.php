<?php

namespace Wyd2016Bundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validator constraints
 */
class UniqueEntities extends Constraint
{
    /** @var string */
    public $message = 'This value is already used.';

    /** @var string */
    public $field;

    /** @var boolean */
    public $ignoreNull = false;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'unique_entities';
    }
}
