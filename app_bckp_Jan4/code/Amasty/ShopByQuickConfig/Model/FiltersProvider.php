<?php

declare(strict_types=1);

namespace Amasty\ShopByQuickConfig\Model;

use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\Collection as FilterSettingCollection;
use Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\CollectionFactory as FilterSettingCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\NoSuchEntityException;

class FiltersProvider
{
    public const CATEGORY_ATTRIBUTE_CODE = 'category_ids';

    public const CATEGORY_FILTER_CODE = 'category';

    /**
     * @var FilterData[]
     */
    private $itemsCache = [];

    /**
     * @var FilterSettingCollectionFactory
     */
    private $filterCollectionFactory;

    /**
     * @var FilterDataFactory
     */
    private $filterDataFactory;

    /**
     * @var array
     */
    private $customFilterCodes;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var FilterableAttributeList
     */
    private $filterableAttributeList;

    /**
     * FiltersProvider constructor.
     *
     * @param FilterSettingCollectionFactory $filterCollectionFactory
     * @param FilterDataFactory $filterDataFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param FilterableAttributeList $filterableAttributeList
     * @param string[] $customFilterCodes
     */
    public function __construct(
        FilterSettingCollectionFactory $filterCollectionFactory,
        FilterDataFactory $filterDataFactory,
        ScopeConfigInterface $scopeConfig,
        FilterableAttributeList $filterableAttributeList,
        array $customFilterCodes = []
    ) {
        $this->filterCollectionFactory = $filterCollectionFactory;
        $this->filterDataFactory = $filterDataFactory;
        $this->customFilterCodes = $customFilterCodes;
        $this->scopeConfig = $scopeConfig;
        $this->filterableAttributeList = $filterableAttributeList;
    }

    /**
     * Clear local registry and variables cache
     */
    public function reset(): void
    {
        $this->itemsCache = [];
    }

    /**
     * @return FilterData[]
     */
    public function getFilterItems(): array
    {
        $this->loadFilterItems();

        return array_values($this->itemsCache);
    }

    private function loadFilterItems(): void
    {
        if (!empty($this->itemsCache)) {
            return;
        }

        $this->loadCustomFilters();
        $this->loadAttributeFilters();
    }

    private function loadCustomFilters(): void
    {
        foreach ($this->customFilterCodes as $filterCode) {
            if ($this->getConfig($filterCode, 'enabled')) {
                $item = $this->filterDataFactory->create();

                $item->setFilterCode($filterCode);
                $item->setLabel($this->getConfig($filterCode, 'label'));
                $item->setPosition((int) $this->getConfig($filterCode, 'position'));
                $item->setTopPosition((int) $this->getConfig($filterCode, 'top_position'));
                $item->setSidePosition((int) $this->getConfig($filterCode, 'side_position'));
                $item->setBlockPosition((int) $this->getConfig($filterCode, 'block_position'));

                $this->itemsCache[$filterCode] = $item;
            }
        }
    }

    /**
     * @param string $filterName
     * @param string $configName
     *
     * @return mixed
     */
    private function getConfig(string $filterName, string $configName)
    {
        return $this->scopeConfig->getValue(
            'amshopby/' . $filterName . '_filter/' . $configName,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    private function loadAttributeFilters(): void
    {
        $attributeCollection = $this->getAttributeCollection();
        $filterCodes = $attributeCollection->getColumnValues(FilterData::FILTER_CODE);
        $blockPositions = $this->getFiltersBlockData($filterCodes);

        /** @var FilterData $filter */
        foreach ($attributeCollection->getItems() as $filter) {
            $filterCode = $filter->getFilterCode();
            if (isset($blockPositions[$filterCode])) {
                $filter->addData($blockPositions[$filterCode]);
            }

            if ($filter->getAttributeCode() === self::CATEGORY_ATTRIBUTE_CODE) {
                $filter->setPosition((int) $this->getConfig(self::CATEGORY_FILTER_CODE, 'position'));
            }
            $this->itemsCache[$filterCode] = $filter;
        }
    }

    /**
     * @return AttributeCollection
     */
    private function getAttributeCollection(): AttributeCollection
    {
        return $this->filterableAttributeList->getList();
    }

    /**
     * @param string[] $filterCodes
     *
     * @return array array(
     *                  array('block_position' => int, 'top_position' => int, 'side_position' => int),
     *              ...)
     */
    private function getFiltersBlockData(array $filterCodes): array
    {
        /** @var FilterSettingCollection $filterCollection */
        $filterCollection = $this->filterCollectionFactory->create();
        $filterCollection->addFieldToFilter('filter_code', ['in' => $filterCodes]);
        $filterCollection->getSelect()
            ->setPart(Select::COLUMNS, [])
            ->columns(
                [
                    FilterSettingInterface::FILTER_SETTING_ID,
                    FilterSettingInterface::FILTER_CODE,
                    FilterSettingInterface::BLOCK_POSITION,
                    FilterSettingInterface::TOP_POSITION,
                    FilterSettingInterface::SIDE_POSITION,
                ]
            );

        $blockPositions = [];
        foreach ($filterCollection->getData() as $data) {
            $filterCode = $data[FilterSettingInterface::FILTER_CODE];

            $blockData[FilterSettingInterface::BLOCK_POSITION] = (int) $data[FilterSettingInterface::BLOCK_POSITION];
            $blockData[FilterSettingInterface::TOP_POSITION] = (int) $data[FilterSettingInterface::TOP_POSITION];
            $blockData[FilterSettingInterface::SIDE_POSITION] = (int) $data[FilterSettingInterface::SIDE_POSITION];

            $blockPositions[$filterCode] = $blockData;
        }

        return $blockPositions;
    }

    /**
     * @param string $filterCode
     *
     * @return FilterData
     * @throws NoSuchEntityException
     */
    public function getItemByCode(string $filterCode): FilterData
    {
        $this->loadFilterItems();

        if (!isset($this->itemsCache[$filterCode])) {
            throw new NoSuchEntityException(__('Can not load Filter with code "%1"', $filterCode));
        }

        return $this->itemsCache[$filterCode];
    }
}
