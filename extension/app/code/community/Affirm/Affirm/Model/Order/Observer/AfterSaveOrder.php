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
 * Class Affirm_Affirm_Model_Order_Observer_AfterSaveOrder
 *
 * After save order confirmation (return from affirm)
 */
class Affirm_Affirm_Model_Order_Observer_AfterSaveOrder
{
    /**
     * Check is affirm payment method
     *
     * @param array $proxyRequest
     * @return bool
     */
    protected function _isAffirmPaymentMethod($proxyRequest)
    {
        return isset($proxyRequest['params']['payment']['method'])
            && $proxyRequest['params']['payment']['method'] == Affirm_Affirm_Model_Payment::METHOD_CODE;
    }

    /**
     * Apply affirm success logic
     *
     * @param Varien_Event_Observer $observer
     */
    public function postDispatchSaveOrder($observer)
    {
        $response = $observer->getControllerAction()->getResponse();
        $session = Mage::helper('affirm')->getCheckoutSession();
        $serializedRequest = $session->getAffirmOrderRequest();
        $proxyRequest = json_decode($serializedRequest,true);
        $checkoutToken = Mage::registry('affirm_token_code');
        $lastOrderId = $session->getLastOrderId();
        //Return, if order was placed before confirmation
        if (!($serializedRequest && $checkoutToken) || !Mage::helper('affirm')->isXhrRequest($proxyRequest)
            || !$this->_isAffirmPaymentMethod($proxyRequest) || !$lastOrderId) {
            return;
        }

        $session->setPreOrderRender(null);
        $session->setLastAffirmSuccess($checkoutToken);
        $session->setAffirmOrderRequest(null);
        $response->setRedirect(Mage::getUrl('checkout/onepage/success'))->sendResponse();
        return;
    }
}
