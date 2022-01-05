<?php
/**
 * Copyright © 2015 Aceturtle . All rights reserved.
 */
namespace Aceturtle\GuestTrack\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	/**
     * @param \Magento\Framework\App\Helper\Context $context
     */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Catalog\Model\Session $catalogSession
	) {
		parent::__construct($context);
		$this->catalogSession = $catalogSession;
	}

	public function getOrderId(){
	return $this->catalogSession->getData('order_id', false);
	}
}
