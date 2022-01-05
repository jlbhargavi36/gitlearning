<?php

namespace Amasty\Mostviewed\Controller\Adminhtml\Product\Group;

use Amasty\Mostviewed\Api\Data\GroupInterface;

class MassDuplicate extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function itemAction(GroupInterface $group)
    {
        $this->repository->duplicate($group->getGroupId());
    }

    /**
     * {@inheritdoc}
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        return __('A total of %1 record(s) have been duplicated.', $collectionSize);
    }
}
