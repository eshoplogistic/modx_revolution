var eShopLogistic = function (config) {
    config = config || {};
    eShopLogistic.superclass.constructor.call(this, config);
};
Ext.extend(eShopLogistic, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('eshoplogistic', eShopLogistic);

eShopLogistic = new eShopLogistic();