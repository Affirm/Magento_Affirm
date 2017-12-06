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
    alert(result);
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


});