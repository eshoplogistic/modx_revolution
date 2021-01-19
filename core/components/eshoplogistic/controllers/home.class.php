<?php
class eShopLogisticHomeManagerController extends modExtraManagerController
{
    public $eShopLogistic;


    public function initialize()
    {
        $path = $this->modx->getOption('eshoplogistic_core_path', null,
        $this->modx->getOption('core_path') . 'components/eshoplogistic/') . 'model/eshoplogistic/';
        $this->eShopLogistic = $this->modx->getService('eshoplogistic', 'eShopLogistic', $path);
        parent::initialize();
    }


    public function getLanguageTopics()
    {
        return array('eshoplogistic:default');
    }


    public function checkPermissions()
    {
        return true;
    }


    public function getPageTitle()
    {
        return $this->modx->lexicon('eshoplogistic');
    }


    public function loadCustomCssJs()
    {
        #$this->addCss($this->eShopLogistic->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->eShopLogistic->config['jsUrl'] . 'mgr/eshoplogistic.js');
        $this->addJavascript($this->eShopLogistic->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->eShopLogistic->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('<script type="text/javascript">
        eShopLogistic.config = ' . json_encode($this->eShopLogistic->config) . ';
        eShopLogistic.config.connector_url = "' . $this->eShopLogistic->config['connectorUrl'] . '";
        Ext.onReady(function() {
            MODx.load({ xtype: "eshoplogistic-page-home"});
        });
        </script>
        ');
    }


    public function getTemplateFile()
    {
        return $this->eShopLogistic->config['templatesPath'] . 'home.tpl';
    }
}