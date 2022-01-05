<?php

namespace Amasty\Mostviewed\Controller\Analytics;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\App\Action\Context;
use Amasty\Mostviewed\Model\Analytics\ClickFactory;
use Amasty\Mostviewed\Api\ClickRepositoryInterface;
use Psr\Log\LoggerInterface;

class Click extends Ctr
{
    public function __construct(
        ClickFactory $tempFactory,
        ClickRepositoryInterface $dataRepository,
        SessionManagerInterface $sessionManager,
        LoggerInterface $logger,
        Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
    ) {
        parent::__construct(
            $tempFactory,
            $dataRepository,
            $sessionManager,
            'product_id',
            $logger,
            $context,
            $formKeyValidator
        );
    }
}
