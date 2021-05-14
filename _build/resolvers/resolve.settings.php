<?php

if ($transport->xpdo) {
    $modx =& $transport->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {

        case xPDOTransport::ACTION_UPGRADE:
			
			$newst = [
				'payment_prepay' => array(
					'value' => '',
					'xtype' => 'textfield',
					'area' => 'eshoplogistic_payment'
				)
			];
			
			foreach ($newst as $k => $data) {
				if (!$tmp = $modx->getObject('modSystemSetting', array('key' => 'eshoplogistic_'.$k))) {
					$tmp = $modx->newObject('modSystemSetting');
				}
				$tmp->fromArray(array(
					'namespace' => 'eshoplogistic',
					'area'      => $data['area'],
					'xtype'     => $data['xtype'],
					'value'     => $data['value'],
					'key'       => 'eshoplogistic_'.$k,
				), '', true, true);
				$tmp->save();
			}
			
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $modx->removeCollection('modSystemSetting', array(
                'namespace' => 'eshoplogistic',
            ));
            break;
    }
}
return true;