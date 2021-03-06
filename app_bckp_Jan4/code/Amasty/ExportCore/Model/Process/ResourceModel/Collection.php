<?php

declare(strict_types=1);

namespace Amasty\ExportCore\Model\Process\ResourceModel;

use Amasty\ExportCore\Model\Process\Process;
use Amasty\ExportCore\Model\Process\ResourceModel\Process as ProcessResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(Process::class, ProcessResource::class);
    }
}
