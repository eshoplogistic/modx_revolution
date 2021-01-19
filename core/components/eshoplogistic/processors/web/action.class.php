<?php

class eShopLogisticActionProcessor extends modProcessor 
{
    public $permission = '';
	public $eShopLogistic;
	public $query;
	
    public function process()   
	{
        $out = [];
		
        $this->eShopLogistic = $this->modx->getService('eShopLogistic');
		
		if(!empty($_POST['text'])) {
			$this->query = ['target' => $_POST['text']];
			$out = $this->Address();
		}
		elseif(!empty($_POST['secret'])) {
			$out = $this->eShopLogistic->Run('order');
		}
		else {
			if(!empty($_POST['service'])) {
				$out = $this->Delivery();
			}
		}			
		
        return $this->modx->toJSON($out);
    }
	
	
	
	
	private function Delivery() 
	{
		if($esl_delivery = $this->eShopLogistic->Run('delivery')) {
			return $esl_delivery;
		}

		return [];
	}
	
	
	
	
	private function Address() 
	{
        $list = [];
				
		$response = $this->eShopLogistic->Query('search', $this->query);
		
		if(!empty($response['data'])) {
								
			foreach ($response['data'] as $item) {

				if(!empty($item['fias'])) {	

					$target = $item['type'].' '.$item['name'];
					
					if(!empty($item['region'])) {
						$target .= ', '.$item['region'];
					}
					if(!empty($item['subregion'])) {
						$target .= ', '.$item['subregion'];
					}

					$list[] = [
						'fias' => $item['fias'],
						'index' => $item['postal_code'] ?? '',
						'target' => $target
					];

				}
			}
		}
			
		return $list;
	}
	
	/* можно использовать dadata.ru
	private function Address() 
	{
        $list = [];
		if($suggest = $this->Suggest()) {
			
			if(!empty($suggest['suggestions'])) {
								
				foreach ($suggest['suggestions'] as $item) {
										
					$fias = (!empty($item['data']['city_fias_id'])) ? $item['data']['city_fias_id'] : $item['data']['settlement_fias_id'];
					
					if(!empty($fias)) {	
						
						$region = '';
						if(!empty($item['data']['region'])) {
							$region = $item['data']['region'].' '.$item['data']['region_type'];
						}
						$street = '';
						if(!empty($item['data']['street'])) {
							$street = $item['data']['street_type'].' '.$item['data']['street'];
						}
						
						$list[] = [
							'fias' => $fias,
							'index' => $item['data']['postal_code'] ?? '',
							'region' => $region,
							'city' => (!empty($item['data']['city_fias_id'])) ? $item['data']['city'] : $item['data']['settlement'],
							'street' => $street,
							'building' => $item['data']['house'] ?? ''
						];
						
					}
				}
			}
		}
						
		return $list;
	}
	
	
	private function Suggest() 
	{
        if ($ch = curl_init('http://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address'))  {
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Token '.$this->modx->getOption('eshoplogistic_dadata_token')
             ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $this->query, 'count' => 15]));
            $result = curl_exec($ch);
            if($result = json_decode($result, true)) {
				return $result;
			}
            curl_close($ch);
        }
				
        return false;
	}
	*/
}

return 'eShopLogisticActionProcessor';
