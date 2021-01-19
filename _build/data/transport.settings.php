<?php
/** @var modX $modx */
/** @var array $sources */

$settings = array();

$tmp = array(
    'api_key' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'eshoplogistic_main',
    ),
	'payment_on' => array(
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'eshoplogistic_main',
    ),
	'session_livetime' => array(
        'value' => 3,
        'xtype' => 'numberfield',
        'area' => 'eshoplogistic_main',
    ),
	'no_delivery_id' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'eshoplogistic_main',
    ),
	'frontend_js' => array(
        'value' => '[[+jsUrl]]web/eshoplogistic.js',
        'xtype' => 'textfield',
        'area' => 'eshoplogistic_main',
    ),
	'frontend_css' => array(
        'value' => '[[+cssUrl]]web/eshoplogistic.css',
        'xtype' => 'textfield',
        'area' => 'eshoplogistic_main',
    ),
	'payment_on' => array(
        'value' => false,
        'xtype' => 'combo-boolean',
        'area' => 'eshoplogistic_payment'
    ),
	'payment_cash' => array(
        'value' => '1',
        'xtype' => 'textfield',
        'area' => 'eshoplogistic_payment',
    ),
	'payment_card' => array(
        'value' => '2',
        'xtype' => 'textfield',
        'area' => 'eshoplogistic_payment',
    ),
	'payment_cashless' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'eshoplogistic_payment',
    ),
	'widget_keys' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area' => 'eshoplogistic_order',
    ),
	'message_ordersuccess' => array(
        'value' => 'Ваш заказ №{num} успешно отправлен.',
        'xtype' => 'textfield',
        'area' => 'eshoplogistic_order',
    ),
	'message_ordererror' => array(
        'value' => 'Ошибка создания заказа',
        'xtype' => 'textfield',
        'area' => 'eshoplogistic_order',
    ),
	'orderprefix' => array(
        'value' => 'ESL-',
        'xtype' => 'textfield',
        'area' => 'eshoplogistic_order',
    )
);

foreach ($tmp as $k => $v) {
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => 'eshoplogistic_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}
unset($tmp);

return $settings;
