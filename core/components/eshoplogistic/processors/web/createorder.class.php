<?php

class eShopLogisticCreateOrderProcessor extends modProcessor {

    public $permission = '';

    public function process()   {
        $data = [];
        $eShopLogistic = $this->modx->getService('eShopLogistic');

        return $this->modx->toJSON([
            'success' => true,
            'message'    => 'Заказ №000 создан'
        ]);
    }

}

return 'eShopLogisticCreateOrderProcessor';
