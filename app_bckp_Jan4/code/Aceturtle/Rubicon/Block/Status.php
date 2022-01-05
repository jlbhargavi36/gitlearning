<?php
namespace Aceturtle\Rubicon\Block;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Framework\Registry;
class Status extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        TemplateContext $context,
        Registry $registry,
        \Magento\Customer\Model\Session $session,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        array $data = [])
    {
        $this->coreRegistry = $registry;
        $this->session = $session;
        $this->order = $order;
        $this->timezone = $timezone;
        parent::__construct($context, $data);
    }
    public function getFormatedDateTime($dateTime)
    {
        $dateTimeZone = $this->timezone->date(new \DateTime($dateTime))->format('d-m-Y');
        return $dateTimeZone;
    }
    public function getOrder()
    {
        /*$customerSession = $this->session;
        $customer_id = $customerSession->getCustomerId();
        $order = $this->order->getCollection()->addAttributeToFilter('customer_id', $customer_id);
       
            $url = 'http://rubiconstg-mb.aceturtle.in/rccore/OrderDetail';

        foreach($order as $orders){
            $incrementId = $orders->getIncrementId();
        }
        $data = array(
            "orderNo"=>"$incrementId"
        );
        $content = json_encode($data);
        $headers = array(
            'Accept: application/json',
            'Accesstoken: dea68f6dffd502c70f62e6730f2b8b3e',
            'method: getorderdetailsfororderid'
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);
        if ($err) {
            $errr = 'error:'.$err;
            echo $err;
        }
        $response = json_decode($response, true);
        return $response;
*/
    }
}
