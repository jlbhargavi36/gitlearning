<?php

namespace Amasty\ElasticSearch\Model\Indexer\RelevanceRule;

class RuleProductIndexer extends AbstractIndexer
{
    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function doExecuteRow($id)
    {
        $this->getIndexBuilder()->reindexByRuleIds([$id]);
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function doExecuteList($ids)
    {
        $this->getIndexBuilder()->reindexByRuleIds($ids);
        $this->getCacheContext()->registerTags($this->getIdentities());
    }
}
