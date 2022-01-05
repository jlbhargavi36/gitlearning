<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Ui\DataProvider\Product\Form\Modifier;

use Amasty\StoreCreditProduct\Model\Product\Attributes;
use Amasty\StoreCreditProduct\Model\Product\Type\StoreCreditProductType;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Directory\Helper\Data;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Element\DataType\Price as TypePrice;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Price implements ModifierInterface
{
    const PRICES_PANEL_NAME = 'amasty-store-credit-prices';

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Data
     */
    private $directoryHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        StoreManagerInterface $storeManager,
        Data $directoryHelper
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->storeManager = $storeManager;
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * @param array $meta
     * @return array
     * @throws NoSuchEntityException
     */
    public function modifyMeta(array $meta): array
    {
        if ($this->locator->getProduct()->getTypeId() === StoreCreditProductType::PRODUCT_TYPE) {
            $meta = $this->initPricesFields($meta);
            $meta = $this->modifyPricesPanel($meta);
        }

        return $meta;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     * @throws NoSuchEntityException
     */
    private function initPricesFields(array $meta): array
    {
        $amountPath = $this->arrayManager->findPath(
            Attributes::PRODUCT_PRICES,
            $meta,
            null,
            'children'
        );

        return $this->arrayManager->merge(
            $amountPath,
            $meta,
            $this->getAmountStructure($amountPath, $meta)
        );
    }

    /**
     * @param string $amountPath
     * @param array $meta
     * @return array
     * @throws NoSuchEntityException
     */
    private function getAmountStructure(string $amountPath, array $meta): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'label' => __('Amounts'),
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'disabled' => false,
                        'sortOrder' => $this->arrayManager->get(
                            $amountPath . '/arguments/data/config/sortOrder',
                            $meta
                        ),
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => [
                        'website_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType' => Text::NAME,
                                        'formElement' => Select::NAME,
                                        'componentType' => Field::NAME,
                                        'dataScope' => 'website_id',
                                        'label' => __('Website'),
                                        'options' => $this->getWebsites(),
                                        'value' => $this->getDefaultWebsite(),
                                        'visible' => $this->isMultiWebsites(),
                                        'disabled' => ($this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite()),
                                    ],
                                ],
                            ],
                        ],
                        'value' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Field::NAME,
                                        'formElement' => Input::NAME,
                                        'dataType' => TypePrice::NAME,
                                        'label' => __('Amount'),
                                        'enableLabel' => true,
                                        'dataScope' => 'value',
                                        'required' => '1',
                                        'validation' => [
                                            'required-entry' => true,
                                            'validate-greater-than-zero' => true,
                                            'validate-number' => true
                                        ],
                                        'addbefore' => $this->locator->getStore()
                                            ->getBaseCurrency()
                                            ->getCurrencySymbol(),
                                    ],
                                ],
                            ],
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType' => Text::NAME,
                                        'label' => '',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array[]
     */
    private function getWebsites(): array
    {
        $websites = [
            [
                'label' => __('All Websites [%1]', $this->directoryHelper->getBaseCurrencyCode()),
                'value' => 0,
            ]
        ];
        $productWebsiteIds = $this->locator->getProduct()->getWebsiteIds();

        foreach ($this->storeManager->getWebsites() as $website) {
            if (!in_array($website->getId(), $productWebsiteIds)) {
                continue;
            }
            $websites[] = [
                'label' => __('%1 [%2]', $website->getName(), $website->getBaseCurrencyCode()),
                'value' => $website->getId(),
            ];
        }

        return $websites;
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    private function getDefaultWebsite(): int
    {
        if ($this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite()) {
            return (int)$this->storeManager->getStore($this->locator->getProduct()->getStoreId())->getWebsiteId();
        }

        return 0;
    }

    /**
     * @return bool
     */
    private function isShowWebsiteColumn(): bool
    {
        if ($this->isScopeGlobal() || $this->storeManager->isSingleStoreMode()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function isAllowChangeWebsite(): bool
    {
        if (!$this->isShowWebsiteColumn() || $this->locator->getProduct()->getStoreId()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function isScopeGlobal(): bool
    {
        return $this->locator->getProduct()
            ->getResource()
            ->getAttribute(Attributes::PRODUCT_PRICES)
            ->isScopeGlobal();
    }

    /**
     * @return bool
     */
    private function isMultiWebsites(): bool
    {
        return !$this->storeManager->isSingleStoreMode();
    }

    /**
     * @param array $meta
     * @return array
     */
    private function modifyPricesPanel(array $meta): array
    {
        if (isset($meta[self::PRICES_PANEL_NAME])) {
            $meta[self::PRICES_PANEL_NAME]['arguments']['data']['config']['sortOrder'] =
                AbstractModifier::GENERAL_PANEL_ORDER + 1;
        }

        return $meta;
    }
}
