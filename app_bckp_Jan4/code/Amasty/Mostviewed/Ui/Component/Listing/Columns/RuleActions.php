<?php

namespace Amasty\Mostviewed\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

class RuleActions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * RuleActions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $itemName = $item['name'] ?? '';
                $item[$name]['edit'] = [
                    'href'  => $this->urlBuilder->getUrl(
                        'amasty_mostviewed/product_group/edit',
                        ['id' => $item['group_id']]
                    ),
                    'label' => __('Edit')
                ];
                $item[$name]['delete'] = [
                    'href'    => $this->urlBuilder->getUrl(
                        'amasty_mostviewed/product_group/delete',
                        ['id' => $item['group_id']]
                    ),
                    'label'   => __('Delete'),
                    'confirm' => [
                        'title'   => __('Delete %1', $itemName),
                        'message' => __('Are you sure you want to delete a %1 record?', $itemName)
                    ]
                ];
            }
        }

        return $dataSource;
    }
}
