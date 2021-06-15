define([
    'jquery',
    'Magento_Payment/js/view/payment/cc-form',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Payment/js/model/credit-card-validation/validator'
], function ($, Component) {
    return Component.extend({
        defaults: {
            template: 'OmniPro_PayU/payment/payu-form'
        },
        context: function() {
            return this;
        },
        getCode: function() {
            return 'payu';
        },
        isActive: function() {
            return true;
        },
        validate: function() {
            var $form = $('#' + this.getCode() + '-form');
            return $form.validation() && $form.validation('isValid');
        }
    });
});