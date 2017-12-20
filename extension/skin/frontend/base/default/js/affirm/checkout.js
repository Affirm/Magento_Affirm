function isAffirmMethod()
{
    var result = false;

    if ($$('input:checked[type=radio][name=payment[method]][value^=affirm]').length) {

        // This is for other checkout modules
        result = true;

    } else if (null != $('iwd_opc_payment_method_select')) {

        // This is for IWD Checkout suite
        result = $('iwd_opc_payment_method_select').value.match(/affirm/);
    }
    return result;
}

document.observe('dom:loaded', function () {

    // This is for Amasty One Step Checkout
    if ($('amscheckout-onepage')) {

        completeCheckout = completeCheckout.wrap(
            function (parentMethod) {

                if (isAffirmMethod()) {
                    callAmastyCheckoutForAffirm();
                } else {
                    return parentMethod();
                }

            }
        );
    }

    if ($('one-step-checkout-form')) {
        if (typeof oscPlaceOrder == 'function') {     // This is for Magestore_Onestepcheckout
            oscPlaceOrder = oscPlaceOrder.wrap(
                function (parentMethod, elem) {
                    if (isAffirmMethod()) {
                        callMageStoreOneStepCheckoutForAffirm();
                    } else {
                        return parentMethod(elem);
                    }

                }
            );
        }

        if(MagecheckoutSecuredCheckoutForm) {
            if (typeof MagecheckoutSecuredCheckoutForm.prototype.placeOrderProcess == 'function') {
                MagecheckoutSecuredCheckoutForm.prototype.placeOrderProcess = MagecheckoutSecuredCheckoutForm.prototype.placeOrderProcess.wrap(
                    function (parentMethod) {
                        if (isAffirmMethod()) {
                            callMagecheckoutSecuredCheckoutForAffirm();
                        } else {
                            return parentMethod();
                        }
                    }
                );
            }
        }
    }


});