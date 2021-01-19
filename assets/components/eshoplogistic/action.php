<?php
if(empty($_POST['secret'])) {
	if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') die;
}

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

$eShopLogistic = $modx->getService('eshoplogistic', 'eShopLogistic', $modx->getOption('eshoplogistic_core_path', null,
    $modx->getOption('core_path') . 'components/eshoplogistic/') . 'model/eshoplogistic/'
);
		
if ($modx->error->hasError() OR !($eShopLogistic instanceof eShopLogistic)) {
    @session_write_close();
    die('Error');
}

$response = $modx->runProcessor('action', $_REQUEST, [
    'processors_path' => $eShopLogistic->config['processorsPath'] . 'web/'
]);
@session_write_close();
exit($response->response);