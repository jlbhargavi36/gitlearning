<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;

class BundlePackConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Session
     */
    private $checkoutSession;

    public function __construct(Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $config = [];

        if ($packIds = $this->checkoutSession->getAppliedPackIds()) {
            $config['applied_bundle_packs'] = $packIds;
        }

        return $config;
    }
}
