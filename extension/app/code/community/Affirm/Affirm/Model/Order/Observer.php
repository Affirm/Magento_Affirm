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
class Affirm_Affirm_Model_Order_Observer
{
    /**
     * Is create order after confirmation
     *
     * @param Affirm_Affirm_Model_Payment $methodInst
     * @return bool
     */
    protected function _isCreateOrderAfterConf($methodInst)
    {
        return ($methodInst->getCode() == Affirm_Affirm_Model_Payment::METHOD_CODE) && $methodInst->redirectPreOrder();
    }

    /**
     * Is create order before confirmation
     *
     * @param Affirm_Affirm_Model_Payment $methodInst
     * @return bool
     */
    protected function _isCreateOrderBeforeConf($methodInst)
    {
        return ($methodInst->getCode() == Affirm_Affirm_Model_Payment::METHOD_CODE) && !$methodInst->redirectPreOrder();
    }

    /**
     * Get route
     *
     * @param string $routeName
     * @return Mage_Core_Controller_Varien_Router_Abstract
     */
    protected function _getRoute($routeName)
    {
        return Mage::app()->getFrontController()->getRouterByRoute($routeName);
    }

    /**
     * Call to pre order affirm action and exit
     *
     * @param Mage_Sales_Model_Order $order
     * @param Mage_Sales_Model_Quote $quote
     */
    protected function _callToPreOrderActionAndExit($order, $quote)
    {
        $request = Mage::app()->getRequest();
        $request->setParams(array('order' => $order, 'quote' => $quote))
            ->setControllerName('payment')
            ->setModuleName('affirm')
            ->setActionName('renderPreOrder')
            ->setDispatched(false);
        $router = $this->_getRoute('standard');
        $router->match($request);
        Mage::app()->getResponse()->sendResponse();
        exit();
    }

    /**
     * Pre order
     *
     * @param Varien_Event_Observer $observer
     */
    public function preOrder($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $methodInst = $order->getPayment()->getMethodInstance();
        if (Mage::helper('affirm')->getAffirmTokenCode()) {
            $methodInst->setAffirmCheckoutToken(Mage::helper('affirm')->getAffirmTokenCode());
        }

        if ($this->_isCreateOrderAfterConf($methodInst)) {
            if (!Mage::helper('affirm')->getAffirmTokenCode()) {
                #ok record the current controller that we are using...
                $request = Mage::app()->getRequest();
                $orderRequest = array('action' => $request->getActionName(),
                    'controller' => $request->getControllerName(),
                    'module'     => $request->getModuleName(),
                    'params'     => $request->getParams(),
                    'method'     => $request->getMethod(),
                    'xhr'        => $request->isXmlHttpRequest(),
                    'POST'       => Mage::app()->getRequest()->getPost(), //need post for some cross site issues
                    'quote_id'   => $quote->getId()
                );
                Mage::helper('affirm')->getCheckoutSession()->setAffirmOrderRequest(serialize($orderRequest));

                $this->_callToPreOrderActionAndExit($order, $quote);
            }
        } elseif ($this->_isCreateOrderBeforeConf($methodInst)) {
            Mage::helper('affirm')->getCheckoutSession()->setAffirmOrderRequest(null);
        }
    }

    /**
     * Reactivate quote
     *
     * @param Varien_Event_Observer $observer
     */
    public function reactivateQuote($observer)
    {
        $quote = $observer->getQuote();
        $methodInst = $quote->getPayment()->getMethodInstance();
        if (($methodInst->getCode() == Affirm_Affirm_Model_Payment::METHOD_CODE) && !$methodInst->redirectPreOrder()) {
            $quote->setIsActive(true);
            $quote->save();
        }
    }
}
