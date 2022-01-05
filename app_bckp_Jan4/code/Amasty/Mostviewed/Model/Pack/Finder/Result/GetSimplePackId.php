<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\Finder\Result;

class GetSimplePackId
{
    /**
     * @var int
     */
    private $lastId = 0;

    public function execute(): int
    {
        return ++$this->lastId;
    }
}
