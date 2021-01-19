<?php

class Order 
{
	public $modx;
    public $eShopLogistic;
	public $ms2;
	
	public $wDataSn = [
		'string' => ['path','comment', 'name','email','phone','deliveryTo','selectedDelivery','idShipper','addressPoint','selectedPayment','addressForDelivery'],
		'int' => ['costDelivery'],
		'double' => ['totalCost','weight'],
		'json' => ['city', 'offers','idShipper','selectedPayment','selectedDelivery']
	];
	
	public $wData = [];
	public $messages = [];
	public $deliveries = [];
		
    	
    function __construct($eShopLogistic) 
	{
        $this->eShopLogistic = &$eShopLogistic;
        $this->modx = &$eShopLogistic->modx;
    }
	
	
	
	
	public function Process($data=[]) 
	{
		$this->messages = [
			$this->modx->getOption('eshoplogistic_message_success'),
			$this->modx->getOption('eshoplogistic_message_error')
		];
		
		
		if(!$this->checkSecret()){
			$this->modx->log(1, '[eShopLogistic] access error: check the secret code of the widget');
			return $this->Response(false, 1);
		}		
		
		$this->acceptData();
		
		$dl = $this->modx->getIterator('msDelivery', ['active' => 1]);
		foreach($dl as $item) {
			$d_ = $item->toArray();
			if(!empty($d_['properties']['service'])) {
				$this->deliveries[$d_['properties']['service']][$d_['properties']['mode']] = $d_['id'];
			}
		}
		
		return $this->createOrder();
	}
	
	
	
	
	
	private function checkSecret() 
	{
		return true;
		
		$secret = (string)$_POST['secret'] ?? '';
		
		if(!empty($secret)) {
			
			$keys = explode(',',trim($this->modx->getOption('eshoplogistic_widget_keys')));

			if(is_array($keys)){
				foreach($keys as $key) {
					if($secret == $key) {
						return true;
					}
				}
			}
		}
		
		return false;
	}
	
	
	
	
	private function acceptData() 
	{
		foreach($this->wDataSn as $type => $items) {
			
			foreach($items as $item) {
				switch ($type) {
					case 'string':
						$this->wData[$item] = filter_input(INPUT_POST, $item, FILTER_SANITIZE_STRING);
						break;
					case 'int':
						$this->wData[$item] = filter_input(INPUT_POST, $item, FILTER_SANITIZE_NUMBER_INT);
						break;
					case 'double':
						$this->wData[$item] = (float)($_POST[$item]);
						break;
					case 'json':
						$this->wData[$item] = json_decode($_POST[$item],1);
						break;
				}
			}
			
		}
		#$this->modx->log(1,print_r($this->wData,1));
	}
	
	
	
	protected function createOrder()
	{		
		if($this->ms2Init()) {
			
			$this->ms2->order->clean();
			
			$order_num = trim($this->modx->getOption('eshoplogistic_orderprefix')).$this->ms2->order->getNum();

			$fields = ['receiver' => '', 'email' => '', 'phone' => '', 'comment' => ''];
				
			foreach($fields as $k => $v) {
				if($k == 'receiver' && !empty($this->wData['name'])) {
					$fields[$k] = $this->wData['name'];
				}
				else {
					if(!empty($this->wData[$k])) {
						$fields[$k] = $this->wData[$k];
					}
				}
				$this->ms2->order->add($k, $fields[$k]); 
			}
        
				
			$comment = [];
			if(!empty($this->wData['city'])) {

				if(!empty($this->wData['city']['name'])) {

					$addr = 'Куда: '.$this->wData['city']['name'];

					if(!empty($this->wData['city']['region'])) {
						$addr .= ', '.$this->wData['city']['region'];
					}
				}
			}
			
			if(!empty($addr)) {
				$comment[] = $addr;
			}
			
			if(!empty($this->wData['idShipper']['name'])) {
				$comment[] = 'Служба доставки: '.$this->wData['idShipper']['name'];
			}
			
			$comment[] = 'Стоимость доставки: '.$this->wData['costDelivery'].' р.' ?? 0;
			
			if(!empty($this->wData['selectedDelivery']['name'])) {
				
				$comment[] = 'Способ доставки: '.$this->wData['selectedDelivery']['name'];
				
				if(!empty($this->wData['addressForDelivery'])) {
					$comment[] = 'Адрес доставки: '.$this->wData['addressForDelivery'];
				}
			}
			
			if(!empty($this->wData['selectedPayment']['name'])) {
				$comment[] = 'Способ оплаты: '.$this->wData['selectedPayment']['name'];
			}

			$comment = implode(' # ', $comment);
			
			if(!empty($fields['comment'])) {	
				$fields['comment'] = $fields['comment'].' ### '.$comment;
			}
			else {
				$fields['comment'] = $comment;
			}
			
			/*
			$this->modx->log(1, print_r($this->wData,1));
			$this->modx->log(1, print_r($this->deliveries,1));
			$this->modx->log(1, print_r($this->eShopLogistic->payments,1));
			die;
			*/
			
			$payment = (!empty($this->eShopLogistic->payments[$this->wData['selectedPayment']['key']][0])) ?
				$this->eShopLogistic->payments[$this->wData['selectedPayment']['key']][0] : 0;

			foreach($this->eShopLogistic->payments as $k => $v) {
				if(!is_array($v)) {
					foreach($v as $pitem) {
						if($pitem == $this->wData['selectedPayment']['key']) {
							$payment = $k;
							break(2);
						}
					}
				}
			}
			
			$orderData = [
				'user_id' => $this->ms2->getCustomerId(),
				'delivery_cost' => $this->wData['costDelivery'],
				'createdon' => date('Y-m-d H:i:s'),
				'num' => $this->modx->getOption('eshoplogistic_orderprefix').$this->ms2->order->getNum(),
				'delivery' => $this->deliveries[$this->wData['idShipper']['keyShipper']][$this->wData['selectedDelivery']['key']] ?? 0,
				'payment' => $payment,
				'status' => 1,
				'context' => $this->ms2->config['ctx'],
				'weight' => $this->wData['weight'],
				'cost' => 0,
				'cart_cost' => 0
			];

			if($this->wData['offers']) {

				foreach ($this->wData['offers'] as $product) {
					$orderData['cart_cost'] += $product['price'] * $product['count'];
				}
				
				$orderData['cost'] = $orderData['cart_cost'] + $this->wData['costDelivery'];

				$order = $this->modx->newObject('msOrder');
				$order->fromArray($orderData);

				$orderProducts = [];
				foreach ($this->wData['offers'] as $product) {
					$orderProduct = $this->modx->newObject('msOrderProduct');
					$orderProduct->fromArray([
						'product_id' => $product['article'],
						'name' => $product['name'],
						'count' => $product['count'],
						'price' => $product['price'],
						'weight' => $product['weight'],
						'cost' => $product['price'] * $product['count']
						//'options' => $item['options']
					]);
					$orderProducts[] = $orderProduct;
				}
				$order->addMany($orderProducts);
				
				$response = $this->ms2->invokeEvent('msOnBeforeCreateOrder', array(
					'msOrder' => $order,
					'order' => $this->ms2->order
				));
				
				if (!$response['success']) {
					$this->modx->log(1, '[eShopLogistic] minishop2 msOnBeforeCreateOrder error: '.$response['message']);
					return $this->Response(false, 1);
				}

				$address = $this->modx->newObject('msOrderAddress');
				$address->fromArray(array_merge(['createdon' => date('Y-m-d H:i:s')], $fields));
				$order->addOne($address);

				if ($order->save()) {
					$this->ms2->invokeEvent('msOnCreateOrder', [
						'msOrder' => $order,
						'order' => $this->ms2->order
					]);
					$this->ms2->order->clean();
					$this->ms2->changeOrderStatus($order->get('id'), 1);

					return $this->Response(true, 0, $order_num);
				}
			}
		}
		else {
			$this->modx->log(1, '[eShopLogistic] minishop2 initialization error');
			return $this->Response(false, 1);
		}
	}
	
	
	
	
	
	protected function ms2Init()
	{
		if(is_dir($this->modx->getOption('core_path').'components/minishop2/model/minishop2/')) {
			
			$this->ms2 = $this->modx->getService('miniShop2');
			
			if ($this->ms2 instanceof miniShop2) {
				
				$context = $this->modx->context->key ? $this->modx->context->key : 'web';
				
				$this->ms2->initialize($context, ['json_response' => true]);
				
				return true;
			}
		}
		
		return false;
	}
	
	
	
	
	
	protected function Response($success, $msg, $order_num=0)
	{
		$message = $this->messages[$msg];
		
		if(!empty($order_num)) {
			$message = str_replace('{num}',$order_num,$message);
		}
				
		return ['success' => $success, 'message' => $message];
	}
	
}

