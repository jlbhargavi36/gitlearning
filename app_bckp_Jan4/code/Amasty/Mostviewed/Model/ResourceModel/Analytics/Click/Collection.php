<?php

namespace Amasty\Mostviewed\Model\ResourceModel\Analytics\Click;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Amasty\Mostviewed\Api\Data\ClickInterface;
use Amasty\Mostviewed\Model\Analytics\Click;
use Amasty\Mostviewed\Model\ResourceModel\Analytics\Click as ClickResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ClickInterface::ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Click::class, ClickResource::class);
    }
}
