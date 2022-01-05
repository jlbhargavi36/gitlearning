<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Model\Attributes\Backend;

use Amasty\StoreCreditProduct\Model\Product\Attributes;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Backend\JsonEncoded;
use Magento\Framework\Exception\LocalizedException;

class Price extends JsonEncoded
{
    public function validate($product)
    {
        $rows = (array)$product->getData($this->getAttribute()->getName());
        $this->clearEmptyValues($rows);

        if (empty($rows)) {
            if (!$product->getData(Attributes::ALLOW_OPEN_AMOUNT)) {
                throw new LocalizedException(__('Amount should be specified or Open Amount should be allowed'));
            } else {
                $this->validateOpenAmount($product);
            }

            return $this;
        }
        $duplicates = [0 => []]; //initialize default website

        foreach ($rows as $row) {
            $websiteId = $row['website_id'];
            $row['value'] = str_replace(',', '', $row['value']);
            $value = (float)$row['value'];

            if (!isset($duplicates[$websiteId])) {
                $duplicates[$websiteId] = [];
            }

            if (in_array($value, $duplicates[$websiteId]) || in_array($value, $duplicates[0])) {
                throw new LocalizedException(__('Duplicate amount found.'));
            } else {
                $duplicates[$websiteId][] = $value;
            }
        }

        if ($product->getData(Attributes::ALLOW_OPEN_AMOUNT)) {
            $this->validateOpenAmount($product);
        }

        return $this;
    }

    /**
     * @param array $rows
     */
    private function clearEmptyValues(array &$rows): void
    {
        foreach ($rows as $key => $row) {
            if (!$row['value']) {
                unset($rows[$key]);
            }
        }
    }

    /**
     * @param Product $product
     *
     * @throws LocalizedException
     */
    private function validateOpenAmount(Product $product): void
    {
        $min = str_replace(',', '', $product->getData(Attributes::OPEN_AMOUNT_MIN));
        $max = str_replace(',', '', $product->getData(Attributes::OPEN_AMOUNT_MAX));

        if ($min && ($min === $max)) {
            throw new LocalizedException(__('Min and Max values of open amount can\'t be equal.'));
        }

        if ($min && ($min > $max)) {
            throw new LocalizedException(__('Min value of open amount must be lower then Max value.'));
        }
    }
}
