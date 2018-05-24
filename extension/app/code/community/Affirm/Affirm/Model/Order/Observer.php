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
    private $_isAffirmOrderSaveAfter = false;
    /**
     * Apply affirm logic
     *
     * @param Varien_Event_Observer $observer
     */
    public function postDispatchAll($observer)
    {
        if($this->_isAffirmOrderSaveAfter) {
            Mage::dispatchEvent('affirm_action_saveorder', $observer->getData());
            $this->_isAffirmOrderSaveAfter = false;
        }
    }

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
     * Pre order to be executed only for redirect checkout flow type
     *
     * @param Varien_Event_Observer $observer
     */
    public function preOrder($observer)
    {
        if (!Mage::helper('affirm')->isCheckoutFlowTypeModal()) {
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
                        'module' => $request->getModuleName(),
                        'params' => $request->getParams(),
                        'method' => $request->getMethod(),
                        'xhr' => $request->isXmlHttpRequest(),
                        'POST' => Mage::app()->getRequest()->getPost(), //need post for some cross site issues
                        'quote_id' => $quote->getId()
                    );
                    $orderRequest['routing_info'] = array(
                        'requested_route' => $request->getRequestedRouteName(),
                        'requested_controller' => $request->getRequestedControllerName(),
                        'requested_action' => $request->getRequestedActionName()
                    );
                    Mage::helper('affirm')->getCheckoutSession()->setAffirmOrderRequest(serialize($orderRequest));
                    $this->_callToPreOrderActionAndExit($order, $quote);
                }
            } elseif ($this->_isCreateOrderBeforeConf($methodInst)) {
                Mage::helper('affirm')->getCheckoutSession()->setAffirmOrderRequest(null);
            }
        }
    }

    /**
     * Reactivate quote
     *
     * @param Varien_Event_Observer $observer
     */
    public function reactivateQuote($observer)
    {
        $this->_isAffirmOrderSaveAfter = true;
        $quote = $observer->getQuote();
        $methodInst = $quote->getPayment()->getMethodInstance();
        if (!Mage::helper('affirm')->getAffirmTokenCode()) {
            Mage::log('Confirm has no checkout token.');
            Mage::getSingleton('core/session')->addError('Payment has failed, please reload checkout page and try again. Checkout token is not available.');
            Mage::app()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'))->sendResponse();
            return;
        } else if (($methodInst->getCode() == Affirm_Affirm_Model_Payment::METHOD_CODE) && !$methodInst->redirectPreOrder()) {
            $quote->setIsActive(true);
            $quote->save();
        }
    }

    /**
     * Modal checkout Before save order
     *
     * @param Varien_Event_Observer $observer
     * @void
     */
    public function preDispatchSaveOrderAction(Varien_Event_Observer $observer)
    {
        if (Mage::helper('affirm')->isCheckoutFlowTypeModal()) {
            /* @var $controller Mage_Core_Controller_Front_Action */
            $controller = $observer->getEvent()->getControllerAction();
            $payment = Mage::helper('affirm')->getCheckoutSession()->getQuote()->getPayment();
            $paymentMethod = $payment->getMethod();
            if($paymentMethod){
                $methodInst = $payment->getMethodInstance();
            } else {
                $dataSavePayment = $controller->getRequest()->getPost('payment', array());
                try {
                    Mage::getSingleton('checkout/type_onepage')->savePayment($dataSavePayment);
                    $payment = Mage::helper('affirm')->getCheckoutSession()->getQuote()->getPayment();
                    $paymentMethod = $payment->getMethod();
                    $methodInst = $payment->getMethodInstance();
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    $controller->setFlag('', Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH, true);
                    $response = array('error' => -1, 'message' => $message);
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
                    $controller->getRequest()->setDispatched(true);
                    return;
                }
            }
            if (!Mage::helper('affirm')->getAffirmTokenCode()) {
                $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
                if ($requiredAgreements && $controller->getRequest()->getPost('agreement', array())) {
                    $postedAgreements = array_keys($controller->getRequest()->getPost('agreement', array()));
                    $diff = array_diff($requiredAgreements, $postedAgreements);
                    if ($diff) {
                        $result['success'] = false;
                        $result['error'] = true;
                        $result['error_messages'] = 'Please agree to all the terms and conditions before placing the order.';
                        $controller->setFlag('', Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH, true);
                        $controller->getResponse()->setBody(
                            Mage::helper('core')->jsonEncode($result)
                        );
                        $controller->getRequest()->setDispatched(true);
                        return;
                    }
                }

                $data = $controller->getRequest()->getPost('payment', array());
                if ($data) {
                    $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
                        | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
                        | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
                        | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
                        | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
                    Mage::helper('affirm')->getCheckoutSession()->getQuote()->getPayment()->importData($data);
                }

                if ($controller->getRequest()->getPost('newsletter')) {
                    Mage::getSingleton('checkout/session')
                        ->setNewsletterSubsribed(true)
                        ->setNewsletterEmail(
                            Mage::helper('affirm')->getCheckoutSession()->getQuote()->getCustomerEmail()
                        );
                }
                #ok record the current controller that we are using...
                $request = Mage::app()->getRequest();
                $post = (Mage::app()->getRequest()->getPost()) ? Mage::app()->getRequest()->getPost() : null;
                $orderRequest = array('action' => $request->getActionName(),
                    'controller' => $request->getControllerName(),
                    'module' => $request->getModuleName(),
                    'params' => $request->getParams(),
                    'method' => $request->getMethod(),
                    'xhr' => $request->isXmlHttpRequest(),
                    'POST' => $post, //need post for some cross site issues
                    'quote_id' => Mage::helper('affirm')->getCheckoutSession()->getQuote()->getId()
                );
                Mage::helper('affirm')->getCheckoutSession()->setAffirmOrderRequest(serialize($orderRequest));
                $controller->setFlag('', Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH, true);
                $controller->getRequest()->setDispatched(true);
                return;
            } else {
                $methodInst->setAffirmCheckoutToken(Mage::helper('affirm')->getAffirmTokenCode());
            }
        }
    }
}
