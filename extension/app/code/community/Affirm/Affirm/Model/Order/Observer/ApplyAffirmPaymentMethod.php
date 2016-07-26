<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category    Affirm
 * @package     Affirm_Affirm
 * @copyright   Copyright (c) 2014 One Pica, Inc. (http://www.onepica.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Affirm_Affirm_Model_Order_Observer_ApplyAffirmPaymentMethod
 *
 * Apply affirm payment method
 */
class Affirm_Affirm_Model_Order_Observer_ApplyAffirmPaymentMethod
{
    /**
     * Apply affirm payment method
     *
     * @param Varien_Event_Observer $observer
     */
    public function execute($observer)
    {
        if ($this->_canApplyAffirmPaymentMethod()) {
            $session = Mage::helper('affirm')->getCheckoutSession();
            $quote = $session->getQuote();
            $payment = $quote->getPayment();
            $data['method'] = Affirm_Affirm_Model_Payment::METHOD_CODE;
            $payment->importData($data);
            $quote->save();
            // remove payment flag from session (payment method will be set only once)
            $session->unsAffirmPaymentFlag();
        }
    }

    /**
     * Check is affirm payment method can be applied
     *
     * @return bool
     */
    protected function _canApplyAffirmPaymentMethod()
    {
        $canApply = Mage::helper('affirm')->getCheckoutSession()->getAffirmPaymentFlag()
                  && Mage::helper('affirm/promo_data')->isCheckoutButtonActive()
                  && Mage::helper('affirm')->isAffirmPaymentMethodAvailable();
        return  $canApply;
    }
}
