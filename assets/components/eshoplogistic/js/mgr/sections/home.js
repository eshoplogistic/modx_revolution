eShopLogistic.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'eshoplogistic-panel-home',
            renderTo: 'eshoplogistic-panel-home-div'
        }]
    });
    eShopLogistic.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(eShopLogistic.page.Home, MODx.Component);
Ext.reg('eshoplogistic-page-home', eShopLogistic.page.Home);