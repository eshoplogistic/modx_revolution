<?php
if (!$eShopLogistic = $modx->getService('eshoplogistic', 'eShopLogistic', $modx->getOption('eshoplogistic_core_path', null,
    $modx->getOption('core_path') . 'components/eshoplogistic/') . 'model/eshoplogistic/', $scriptProperties)
) {
    return 'Could not load eShopLogistic class!';
}

$eShopLogistic->initialize('web');

return;