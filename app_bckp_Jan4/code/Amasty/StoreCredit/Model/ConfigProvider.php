<?php

namespace Amasty\StoreCredit\Model;

use Amasty\StoreCredit\Model\Config\Utils;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    /**
     * @var Utils
     */
    private $utils;

    protected $pathPrefix = 'amstorecredit/';

    /**#@+
     * Constants defined for xpath of system configuration
     */
    const XPATH_ENABLED = 'general/enabled';
    const REFUND_AUTOMATICALLY = 'general/refund_automatically';
    const ALLOW_ON_TAX = 'general/allow_on_tax';
    const ALLOW_ON_SHIPPING = 'general/allow_on_shipping';

    const RESTRICT_PRODUCTS = 'usage/restrict';
    const RESTRICT_ACTION = 'usage/action';
    const PRODUCT_SKUS = 'usage/skus';
    const CATEGORY_IDS = 'usage/category_ids';
    const TOOLTIP_ENABLED = 'usage/use_tooltip';
    const TOOLTIP_TEXT = 'usage/tooltip_text';
    const ENCOURAGE = 'usage/encourage';

    const EMAIL_ENABLED = 'email/enabled';
    const EMAIL_ACTIONS = 'email/actions';
    const EMAIL_SENDER = 'email/sender';
    const EMAIL_REPLY = 'email/reply';
    const EMAIL_TEMPLATE = 'email/template';
    /**#@-*/

    public function __construct(Utils $utils, ScopeConfigInterface $scopeConfig)
    {
        parent::__construct($scopeConfig);
        $this->utils = $utils;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isSetFlag(self::XPATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function isRefundAutomatically()
    {
        return $this->isSetFlag(self::REFUND_AUTOMATICALLY);
    }

    /**
     * @var int $storeId
     *
     * @return bool
     */
    public function isAllowOnTax($storeId = null)
    {
        return $this->isSetFlag(self::ALLOW_ON_TAX, $storeId);
    }

    /**
     * @var int $storeId
     *
     * @return bool
     */
    public function isAllowOnShipping($storeId = null)
    {
        return $this->isSetFlag(self::ALLOW_ON_SHIPPING, $storeId);
    }

    /**
     * @return bool
     */
    public function isEmailEnabled()
    {
        return $this->isSetFlag(self::EMAIL_ENABLED);
    }

    /**
     * @return array
     */
    public function getEmailActions()
    {
        return explode(',', $this->getValue(self::EMAIL_ACTIONS));
    }

    /**
     * @return string
     */
    public function getEmailSender()
    {
        return $this->getValue(self::EMAIL_SENDER);
    }

    /**
     * @return string
     */
    public function getEmailReplyTo()
    {
        return $this->getValue(self::EMAIL_REPLY);
    }

    /**
     * @return string
     */
    public function getEmailTemplate($storeId = 0)
    {
        return $this->getValue(self::EMAIL_TEMPLATE, $storeId);
    }

    public function getConfigValue(...$args)
    {
        return $this->getValue(...$args);
    }

    public function getGlobalConfigValue($path)
    {
        $this->scopeConfig->getValue($this->pathPrefix . $path);
    }

    /**
     * @return bool
     */
    public function isRestrictProductsEnabled(): bool
    {
        return $this->isSetFlag(self::RESTRICT_PRODUCTS);
    }

    /**
     * @return int
     */
    public function getRestrictAction(): int
    {
        return (int) $this->getValue(self::RESTRICT_ACTION);
    }

    /**
     * @return array
     */
    public function getProductSkusForRestrict(): array
    {
        return $this->utils->convertToArray((string) $this->getValue(self::PRODUCT_SKUS));
    }

    /**
     * @return array
     */
    public function getCategoryIdsForRestrict(): array
    {
        return $this->utils->convertToArray((string) $this->getValue(self::CATEGORY_IDS));
    }

    /**
     * @return bool
     */
    public function isTooltipEnabled(): bool
    {
        return $this->isSetFlag(self::TOOLTIP_ENABLED);
    }

    /**
     * @return string
     */
    public function getTooltipText(): string
    {
        return (string) $this->getValue(self::TOOLTIP_TEXT);
    }

    /**
     * @return bool
     */
    public function isEncourage(): bool
    {
        return $this->isSetFlag(self::ENCOURAGE);
    }
}
