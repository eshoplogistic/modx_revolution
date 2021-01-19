<?php
class Delivery 
{
	public $modx;
    public $eShopLogistic;
	public $data = [
		'mode' => '',
		'service' => '',
		'payment' => '',
		'fias' => ''
	];

	
    function __construct($eShopLogistic) 
	{
        $this->eShopLogistic = &$eShopLogistic;
        $this->modx = &$eShopLogistic->modx;
		
		$this->data['fias'] = $_SESSION['eShopLogistic']['fias'] ?? '';
    }
	
	
	
	
	public function Process($data=[]) 
	{				
		$cost = $data['cost'] ?? 0;
		
		$check = $this->Prepare($data);
		$offers = $this->Offers();

		if($check && $offers) {
			
			$to = $_SESSION['eShopLogistic']['to'][$this->data['fias']][$this->data['service']];
			if($this->data['service'] == 'postrf' && !empty($data['order']['index'])) {
				$to = $data['order']['index'];
			}
			
			
			$query = [
				'from' => $_SESSION['eShopLogistic']['from'][$this->data['service']],
				'to' => $to,
				'payment' => $this->data['payment'],
				'offers' => json_encode($offers)
			];
			

			$hash = md5(implode('.', $query));
		
			if(empty($_SESSION['eShopLogistic'][$hash][$this->data['service']][$this->data['mode']]['price'])) {						
				$this->Delivery($query, $hash);
			}
			
			if(isset($_SESSION['eShopLogistic'][$hash][$this->data['service']][$this->data['mode']]['price'])) {
				
				$currency = $this->modx->lexicon('eshoplogistic_frontend_currency');
				$is_free = 0;
				if($_SESSION['eShopLogistic'][$hash][$this->data['service']][$this->data['mode']]['price'] == 0) {
					$_SESSION['eShopLogistic'][$hash][$this->data['service']][$this->data['mode']]['price'] = $this->modx->lexicon('eshoplogistic_frontend_free');
					$currency = '';
					$is_free = 1;
				}
				
				$terminals = [];
				
				if($this->data['mode'] == 'terminal') {
					if(!empty($_SESSION['eShopLogistic'][$hash][$this->data['service']]['terminals'])) {
						$terminals = $_SESSION['eShopLogistic'][$hash][$this->data['service']]['terminals'];
					}
				}
			
				# уберём примечания
				/*foreach($terminals as $k => $terminal) {
					$terminals[$k]['note'] = '';
				}*/  
								
				return [
					'target' => $_SESSION['eShopLogistic']['target'],
					'fias' => $this->data['fias'],
					'service' => $this->data['service'],
					'mode' => $this->data['mode'],
					'payment' => $this->data['payment'],
					'price' => $_SESSION['eShopLogistic'][$hash][$this->data['service']][$this->data['mode']]['price'],
					'cost' => $cost + $_SESSION['eShopLogistic'][$hash][$this->data['service']][$this->data['mode']]['price'],
					'time' => $_SESSION['eShopLogistic'][$hash][$this->data['service']][$this->data['mode']]['time'] ?? '',
					'terminals' => $terminals,
					'note' => $_SESSION['eShopLogistic'][$hash][$this->data['service']][$this->data['mode']]['comment'],
					'currency' => $currency,
					'is_free' => $is_free
				];
				
			}
			
		}
					
		return ['cost' => $cost];
	}
	
	
	
	
	
	private function Delivery($query, $hash) 
	{
		$response = $this->eShopLogistic->Query('delivery/'.$this->data['service'], $query);
				
		#$this->modx->log(1,print_r($query,1).' ---> '.print_r($response['data'],1));
		
		if(isset($response['data'][$this->data['mode']]['price'])) {
			$_SESSION['eShopLogistic'][$hash][$this->data['service']] = $response['data'];
			return true;
		}

		return false;
	}
	
	
	
	
	private function Prepare($data) 
	{
		#unset($_SESSION['eShopLogistic']);
		#$_POST['fias'] = 'bb035cc3-1dc2-4627-9d25-a1bf2d4b936b';
		#unset($data['delivery']);
				
		foreach (['mode', 'service', 'fias'] as $item) {
			if(empty($data['delivery']['properties'][$item])) {
				if(!empty($_POST[$item])) {
					$this->data[$item] = $_POST[$item];
				}
				else {
					if($item != 'fias') {
						if(!empty($data['order']['delivery'])) {
							if(empty($_SESSION['eShopLogistic']['deliveries'])) {
								if($deliveries = $this->modx->getCollection('msDelivery')) {
									foreach($deliveries as $delivery) {
										$_SESSION['eShopLogistic']['deliveries'][$delivery->get('id')] = $delivery->get('properties');
									}
								}
							}
							else {
								if(!empty($_SESSION['eShopLogistic']['deliveries'][$data['order']['delivery']][$item])) {
									$this->data[$item] = $_SESSION['eShopLogistic']['deliveries'][$data['order']['delivery']][$item];
								}
							}
						}
					}
				}
			}
			else {
				$this->data[$item] = $data['delivery']['properties'][$item] ?? '';
			}
		}
		
		#$this->modx->log(1,'DATA: '.print_r($this->data,1));
		
		if(empty($this->data['service']) || empty($this->data['mode'])) { return false; }
		
		if(!empty($eshoplogistic_payment_on = $this->modx->getOption('eshoplogistic_payment_on'))) {
			$payment = '';
			if(!empty($_POST['payment'])) {
				$payment = @$_POST['payment'];
			}
			else {
				if(!empty($data['order']['payment'])) {
					$payment = $data['order']['payment'];
				}
			}
			if(!empty($payment)) {
				$this->data['payment'] = $this->eShopLogistic->Payment($payment);
			}
		}
		
		if(empty($_SESSION['eShopLogistic']['from']) || 
			empty($_SESSION['eShopLogistic']['to'][$this->data['fias']][$this->data['service']])) {
			$this->eShopLogistic->setTarget($this->data['fias']);
		}
		else {
			if(!empty($_SESSION['eShopLogistic']['fias'])) {
				if($_SESSION['eShopLogistic']['fias'] != $this->data['fias']) {
					$this->eShopLogistic->setTarget($this->data['fias']);
				}
			}
		}
				
		#$this->modx->log(1,'SESS: '.print_r($_SESSION['eShopLogistic'],1));
		
		if(empty($_SESSION['eShopLogistic']['from'][$this->data['service']] ||
			empty($_SESSION['eShopLogistic']['to'][$this->data['fias']][$this->data['service']]))) {
			return false;
		}
			
		return true;
	}
	
	
	

	
	private function Offers() 
	{
		if(!empty($_SESSION['minishop2']['cart'])) {
			foreach($_SESSION['minishop2']['cart'] as $item) {
				$offers[] = [
					'article' => $item['id'],
					'price' => $item['price'],
					'count' => $item['count'],
					'weight' => $item['weight'] ?: 1,
					'dimensions' => $item['dimensions'] ?? ''
				];
			}
		}				
		return $offers ?? false;
	}
	
	
	
	
	
	
	

}