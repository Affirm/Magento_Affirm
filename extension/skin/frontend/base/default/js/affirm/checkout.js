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

    if($('iwd_opc_place_order_button')){
        if (typeof OnePage.prototype.saveOrder == 'function') {     // This is for IWD OPC
            OnePage.prototype.saveOrder = OnePage.prototype.saveOrder.wrap(
                function (parentMethod) {
                    if (isAffirmMethod()) {
                        callIWDOneStepCheckoutForAffirm();
                    } else {
                        return parentMethod();
                    }

                }
            );
        }
    }

    // This is for Firecheckout
    // And Venedor Theme 1.6.3
    if ($('firecheckout-form') || $('onepagecheckout_orderform')) {
        if (typeof checkout.save == 'function') {     // This is for TM Firecheckout
            checkout.save = checkout.save.wrap(
                function (parentMethod) {
                    if (isAffirmMethod()) {
                        var createAccount = $('billing:register_account');
                        billing.setCreateAccount(createAccount ? createAccount.checked : 1); // create account if checkbox is missing
                        callTMFireCheckoutForAffirm();
                    } else {
                        return parentMethod();
                    }

                }
            );
        }
    }
});

window.addEventListener('load', function() {
    if ($('onestep_form')) {
        if (typeof window.OneStep.Views.Init.prototype.updateOrder == 'function') {
            window.OneStep.Views.Init.prototype.updateOrder = window.OneStep.Views.Init.prototype.updateOrder.wrap(
                function (parentMethod) {
                    if (isAffirmMethod()) {
                        callMageWorldCheckoutForAffirm();
                    } else {
                        return parentMethod();
                    }

                }
            )
        }
    }
});