<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
}
else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$eShopLogistic = $modx->getService('eshoplogistic', 'eShopLogistic', $modx->getOption('eshoplogistic_core_path', null,
        $modx->getOption('core_path') . 'components/eshoplogistic/') . 'model/eshoplogistic/'
);
$modx->lexicon->load('eshoplogistic:default');


$corePath = $modx->getOption('eshoplogistic_core_path', null, $modx->getOption('core_path') . 'components/eshoplogistic/');
$path = $modx->getOption('processorsPath', $eShopLogistic->config, $corePath . 'processors/');
$modx->getRequest();

$request = $modx->request;
$request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));