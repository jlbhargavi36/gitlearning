<?php

declare(strict_types=1);

namespace Amasty\ImportCore\Import\DataHandling\FieldModifier;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ImportCore\Import\DataHandling\AbstractModifier;
use Amasty\ImportCore\Import\DataHandling\ModifierProvider;

class Lowercase extends AbstractModifier implements FieldModifierInterface
{
    public function transform($value)
    {
        if (empty($value) || !is_string($value)) {
            return $value;
        }

        return mb_strtolower($value);
    }

    public function getGroup(): string
    {
        return ModifierProvider::TEXT_GROUP;
    }

    public function getLabel(): string
    {
        return __('Lower Case')->getText();
    }
}
