<?php

namespace Amasty\StoreCredit\Api\Data;

interface SalesFieldInterface
{
    const AMSC_USE = 'amstorecredit_use';
    const AMSC_BASE_AMOUNT = 'amstorecredit_base_amount';
    const AMSC_AMOUNT = 'amstorecredit_amount';
    const AMSC_SHIPPING_AMOUNT = 'amstorecredit_shipping_amount';
    const AMSC_SHIPPING_AMOUNT_INVOICED = 'amstorecredit_shipping_amount_invoiced';
    const AMSC_SHIPPING_AMOUNT_REFUNDED = 'amstorecredit_shipping_amount_refunded';
    const AMSC_INVOICED_BASE_AMOUNT = 'amstorecredit_invoiced_base_amount';
    const AMSC_INVOICED_AMOUNT = 'amstorecredit_invoiced_amount';
    const AMSC_REFUNDED_BASE_AMOUNT = 'amstorecredit_refunded_base_amount';
    const AMSC_REFUNDED_AMOUNT = 'amstorecredit_refunded_amount';
}
