<?php

namespace Amasty\ExportCore\Api\Filter;

use Amasty\ExportCore\Api\Config\Entity\Field\FieldInterface;
use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;

interface FilterMetaInterface
{
    public function getJsConfig(FieldInterface $field): array;

    public function getConditions(FieldInterface $field): array;

    public function prepareConfig(FieldFilterInterface $filter, $value): FilterMetaInterface;

    public function getValue(FieldFilterInterface $filter);
}
