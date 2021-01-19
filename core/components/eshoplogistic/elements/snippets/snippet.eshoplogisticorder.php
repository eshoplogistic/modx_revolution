<?php
if (!$eShopLogistic = $modx->getService('eshoplogistic', 'eShopLogistic', $modx->getOption('eshoplogistic_core_path', null,
    $modx->getOption('core_path') . 'components/eshoplogistic/') . 'model/eshoplogistic/', $scriptProperties)
) {
    return 'Could not load eShopLogistic class!';
}

$fias = $modx->getOption('fias', $scriptProperties, '');
$city = $modx->getOption('city', $scriptProperties, '');

return $eShopLogistic->Init($fias, $city);
