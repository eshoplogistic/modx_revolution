<?php
switch ($modx->event->name) {
	
	case 'msOnCreateOrder':
		
		$data_ = $order->get();
		
		if(!empty($data_['terminal']) 
			&& !empty($data_['delivery'])){
			
			if($delivery = $modx->getObject('msDelivery',$data_['delivery'])) {
			
				$properties = $delivery->get('properties');
				
				if(!empty($properties['mode'])) {
					if($properties['mode'] == 'terminal') {
						$address = $msOrder->getOne('Address');	
						$comment = $address->get('comment').' Пункт самовывоза: '.$data_['terminal'];
						$address->set('comment',$comment);
						$address->save();
					}
				}
			
			}
			
		}
		
		break;
	
}