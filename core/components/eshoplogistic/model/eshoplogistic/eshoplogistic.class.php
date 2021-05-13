<?php
class eShopLogistic
{

    public $modx;
	public $pdoTools;
	public $version = '0.0.11';
	
	public $payments;

    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;
		$this->pdoTools = $this->modx->getService('pdoTools');
		
		$this->payments = [
			'cash' => explode(',', $this->modx->getOption('eshoplogistic_payment_cash')),
			'card' => explode(',', $this->modx->getOption('eshoplogistic_payment_card')),
			'cashless' => explode(',', $this->modx->getOption('eshoplogistic_payment_cashless'))
		];

        $corePath = $this->modx->getOption('eshoplogistic_core_path', $config,
            $this->modx->getOption('core_path') . 'components/eshoplogistic/'
        );
        $assetsUrl = $this->modx->getOption('eshoplogistic_assets_url', $config,
            $this->modx->getOption('assets_url') . 'components/eshoplogistic/'
        );
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
			'actionUrl' => $assetsUrl . 'action.php',
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $connectorUrl,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',
        ), $config);

        $this->modx->addPackage('eshoplogistic', $this->config['modelPath']);
        $this->modx->lexicon->load('eshoplogistic:default');
    }
	
	
	
	
	
	public function initialize($ctx = 'web', array $scriptProperties = array()) 
	{
        if (isset($this->initialized[$ctx])) {
            return $this->initialized[$ctx];
        }

        $this->config = array_merge($this->config, $scriptProperties, array('ctx' => $ctx));

        if ($ctx != 'mgr' AND (!defined('MODX_API_MODE') OR !MODX_API_MODE)) {}
        
        $config = $this->pdoTools->makePlaceholders($this->config);
        
        $data = json_encode([
            'actionUrl' => $this->config['actionUrl'],
            'assetsUrl' => $this->config['assetsUrl'],
            'payment_on' => $this->modx->getOption('eshoplogistic_payment_on')
        ], true);
        $this->modx->regClientStartupScript('<script type="text/javascript">eShopLogisticConfig = '.$data.';</script>', true);
        
		$css = trim($this->modx->getOption('eshoplogistic_frontend_css'));
        if (!empty($css) && preg_match('/\.css$/i', $css)) {
            $this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css.'?v='.$this->version));
        }
        
        $js = trim($this->modx->getOption('eshoplogistic_frontend_js'));
        if (!empty($js) && preg_match('/\.js/i', $js)) {
            $this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js.'?v='.$this->version)); //time())); 
        }
        
        $initialize = true;
        $this->initialized[$ctx] = $initialize;

        return $initialize;
    }
	
	
	
	
	
	
	public function Run($handler = 'delivery', $data = []) 
	{
        $class = ucfirst($handler);

		$file = dirname(__FILE__) . '/handlers/'.$handler.'.php';
		if(file_exists($file)) {
			require_once $file;
			$class = new $class($this);
			return $class->Process($data);
		}
		return false;
	}
	
	
	
	
	
	public function Init($fias='', $city='') 
	{		
		$services = [];
		
		#unset($_SESSION['eShopLogistic']);
		
		# время жизни сессии, 3 часа
		$lt = $this->modx->getOption('eshoplogistic_session_livetime') ?? 3;
		$livetime = time() + $lt * 60 * 60; 
		
		if(!empty($_SESSION['eShopLogistic']['livetime'])) {
			if($_SESSION['eShopLogistic']['livetime'] <= time()) {
				unset($_SESSION['eShopLogistic']);
			}
		}
		
		if(empty($_SESSION['eShopLogistic']['livetime'])) {
			$_SESSION['eShopLogistic']['livetime'] = $livetime;
		}
		
		
		if(empty($_SESSION['eShopLogistic']['init'])) {
			$response = $this->Query('init');

			if(!empty($response['data'])) {
				$_SESSION['eShopLogistic']['init'] = $response['data'];
			}
		}
		
		if(!empty($_SESSION['eShopLogistic']['init'])) {
		    
    		foreach($_SESSION['eShopLogistic']['init'] as $service => $data) {
    		    
    			$services[$service]['comment'] =  $data['comment'] ?? '';
    			$services[$service]['payments'] = [];
    			
    			if(!empty($data['payments'])) {
    			    
    				foreach ($data['payments'] as $payment) {
    				    
    					if(!empty($this->payments[$payment['key']])) {
    
    						foreach($this->payments[$payment['key']] as $item) {
    						    
    						    $services['payments_comments'][$item][] = [
									'key' => $payment['key'],
									'service' => $service,
									'value' => $payment['comment']
								];
    						    
    							if(!empty($item)) {
    							    
    							    if(!in_array($item, $services[$service]['payments'])) {
    								    $services[$service]['payments'][] = $item;
    							    }
    							    
    							}
    							
    						}
    						
    					}
    					
    				}
    				
    			}
    			
    			$services[$service]['payments'] = json_encode($services[$service]['payments'],JSON_NUMERIC_CHECK);
    		}
    		
		}
		
		if(empty($_SESSION['eShopLogistic']['target'])) {
			$this->setTarget($fias, $city);
		}
		
		#$this->modx->log(1,print_r($_SESSION['eShopLogistic'],1));
		
		return $services;
	}
	
	
	
	
	public function Payment($payment) 
	{
		$pmnt = '';
		if(!empty($payment)) {
			foreach($this->payments as $k => $v) {
				if(in_array($payment,$v)) {
					$pmnt = $k;
					break;
				}
			}
		}
		return $pmnt;
	}
	
	
	
	
	public function setTarget($fias = '', $city = '')
	{
		if(!empty($fias)) {
			$search = [
				'fias' => $fias
			];
		}
		elseif(!empty($city)) {
			$search = [
				'city' => $city
			];
		}
		else {
			$search = [
				'ip' => $_SERVER['REMOTE_ADDR']
			];
		}
				
		$response = $this->Query('target', $search);
						
		if(!empty($response['data']['from']) && !empty($response['data']['to'])) {
			$_SESSION['eShopLogistic']['target'] = $response['data']['target'];
			$_SESSION['eShopLogistic']['fias'] = $response['data']['fias'];
			$_SESSION['eShopLogistic']['from'] = $response['data']['from'];
			$_SESSION['eShopLogistic']['to'][$response['data']['fias']] = $response['data']['to'];
		}
	}
	
	
	
	
	public function Query($method='init', $data=[]) 
	{			
		$apiKey = $this->modx->getOption('eshoplogistic_api_key');
		if(empty($apiKey)) {
			$this->modx->log(1, 'eShopLogistic: необходимо указать Ключ API');
			return [];
		}
		
		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.eshoplogistic.ru/api/'.$method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array_merge($data,['key' => $apiKey]));
		$result = curl_exec($curl); 
	    curl_close($curl); 
	    
		return json_decode($result,1);
	}

}