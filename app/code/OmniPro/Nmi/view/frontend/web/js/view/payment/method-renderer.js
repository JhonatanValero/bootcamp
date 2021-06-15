define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (
    Component,
    rendererList
) {
    'use strict'
    rendererList.push(
        {
            'type': 'nmi',
            'component': 'OmniPro_Nmi/js/view/payment/method-renderer/nmi-method'
        }
    );
    return Component.extend({});
});