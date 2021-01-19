<?php

class eslOrderHandler extends msOrderHandler 
{
	
    public function getCost($with_cart = true, $only_cost = false)
    {		
		$response = $this->ms2->invokeEvent('msOnBeforeGetOrderCost', array(
            'order' => $this,
            'cart' => $this->ms2->cart,
            'with_cart' => $with_cart,
            'only_cost' => $only_cost,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }

        $cart = $this->ms2->cart->status();
		$cart_cost = $cart['total_cost'];
        $cost = $with_cart ? $cart_cost : 0;
				
		$cost_ = [];
        if (!empty($this->order['delivery']) && $delivery = $this->modx->getObject('msDelivery', array('id' => $this->order['delivery']))) {
            $cost_ = $delivery->getCost($this, $cost);
			$cost = $cost_['cost'];
        }
		
        if (!empty($this->order['payment']) && $payment = $this->modx->getObject('msPayment', array('id' => $this->order['payment']))) {
            $cost = $payment->getCost($this, $cost);
        }

        $response = $this->ms2->invokeEvent('msOnGetOrderCost', array(
            'order' => $this,
            'cart' => $this->ms2->cart,
            'with_cart' => $with_cart,
            'only_cost' => $only_cost,
            'cost' => $cost,
        ));
        if (!$response['success']) {
            return $this->error($response['message']);
        }
		
        $cost = $response['data']['cost'];
		
		
		$this->modx->lexicon->load('eshoplogistic:default');
		
		$delivery_price = $delivery_data = $delivery_mode = $service_name = false;		
		if(!empty($cost_)) {
			$delivery_data = $cost_;
			if(!empty($delivery_data['price'])) {
				$delivery_price = $this->ms2->formatPrice($delivery_data['price']);
			}
			if(!empty($delivery_data['mode'])) {
				$service_name = $this->modx->lexicon('eshoplogistic_frontend_service_'.$delivery_data['service']);
				$delivery_mode = $this->modx->lexicon('eshoplogistic_frontend_mode_'.$delivery_data['mode'], ['service' => $service_name]);
			}
			
			if(empty($delivery_data['mode'])) {
    		    $delivery_data = [];
    			$delivery_mode = $this->modx->lexicon('eshoplogistic_frontend_no_data_mode');
    			$delivery_data['price'] = $this->modx->lexicon('eshoplogistic_frontend_no_data_price');
    			$delivery_data['currency'] = '';
    		}
		}
		

        return $only_cost
            ? $cost
            : $this->success('', [
				'cost' => $cost, 
				'costf' => $this->ms2->formatPrice($cost), 
				'cartCost' => $this->ms2->formatPrice($cart_cost),
				'deliveryCost' => $delivery_data['price'],
				'deliveryMode' => $delivery_mode,
				'serviceName' => $service_name,
				'delivery' => $delivery_data
			]);
    }
	
	

}

