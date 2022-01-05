<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\OptionSource;

use Amasty\Mostviewed\Model\ResourceModel\Pack\LoadPacksOptions;
use Magento\Framework\Data\OptionSourceInterface;

class Packs implements OptionSourceInterface
{
    /**
     * @var LoadPacksOptions
     */
    private $loadPacksOptions;

    public function __construct(LoadPacksOptions $loadPacksOptions)
    {
        $this->loadPacksOptions = $loadPacksOptions;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->loadPacksOptions->execute() as $packId => $packName) {
            $options[] = [
                'value' => $packId,
                'label' => $packName
            ];
        }

        return $options;
    }
}
