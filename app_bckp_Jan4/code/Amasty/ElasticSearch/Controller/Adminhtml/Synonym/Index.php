<?php

namespace Amasty\ElasticSearch\Controller\Adminhtml\Synonym;

use Magento\Framework\Controller\ResultFactory;

class Index extends AbstractSynonym
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)
            ->getConfig()->getTitle()->prepend(__('Manage Synonyms'));

        return $resultPage;
    }
}
