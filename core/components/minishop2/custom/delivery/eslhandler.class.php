<?php
if (!class_exists('msDeliveryInterface')) {
    require_once dirname(dirname(dirname(__FILE__))) . '/model/minishop2/msdeliveryhandler.class.php';
}

class eslHandler extends msDeliveryHandler implements msDeliveryInterface
{
   
    public function getCost(msOrderInterface $order, msDelivery $delivery, $cost = 0.0)
    {
        $eShopLogistic = $this->modx->getService('eshoplogistic', 'eShopLogistic', $this->modx->getOption('eshoplogistic_core_path', null,
			$this->modx->getOption('core_path') . 'components/eshoplogistic/') . 'model/eshoplogistic/', []);
		
		$data['order'] = $this->ms2->order->get();
        $data['delivery'] = $delivery->toArray();
		$data['cost'] = $cost;
				      
		if($esl_delivery = $eShopLogistic->Run('delivery', $data)) {
			return $esl_delivery;
		}	
				
		return [
			'cost' => parent::getCost($order, $delivery, $cost)
		];
    }

}

