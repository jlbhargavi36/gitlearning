<?php

namespace Amasty\ElasticSearch\Model;

class GetNonTextAttributes
{
    /**
     * @var array
     */
    private $nonTextAttributes = [
        '*',
        'price',
        'visibility',
        'tax_class_id',
        'category_ids'
    ];

    public function __construct(
        array $additionalAttributes = []
    ) {
        $this->nonTextAttributes = array_merge($this->nonTextAttributes, $additionalAttributes);
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->nonTextAttributes;
    }
}
