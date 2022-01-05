<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\Analytic;

use Amasty\Mostviewed\Api\Data\PackInterface;

class IsPackBad
{
    const BAD_ZONE = 3;

    /**
     * @var GetZoneNumber
     */
    private $getZoneNumber;

    /**
     * @var GetOrdersCount
     */
    private $getOrdersCount;

    public function __construct(
        GetZoneNumber $getZoneNumber,
        GetOrdersCount $getOrdersCount
    ) {
        $this->getZoneNumber = $getZoneNumber;
        $this->getOrdersCount = $getOrdersCount;
    }

    public function execute(PackInterface $pack): bool
    {
        $zone = $this->getZoneNumber->execute(
            $this->getOrdersCount->execute((int) $pack->getPackId())
        );

        return $zone && $zone <= self::BAD_ZONE;
    }
}
