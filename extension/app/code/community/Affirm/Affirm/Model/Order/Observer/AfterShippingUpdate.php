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
 * Class Affirm_Affirm_Model_Order_Observer_AfterShippingUpdate
 */
class Affirm_Affirm_Model_Order_Observer_AfterShippingUpdate
{
    /**
     * After Save Order Address in admin to update Affirm with new shipping address
     * @param $observer
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $address = $observer->getEvent()->getAddress();
        $order = $address->getOrder();
        $payment_method_code = $order->getPayment()->getMethodInstance()->getCode();
        if( $payment_method_code == Affirm_Affirm_Model_Payment::METHOD_CODE ){
            $orderAddress = $order->getShippingAddress();
            $street = $orderAddress->getStreet();
            $chargeId = $order->getPayment()->getAdditionalInformation('charge_id');

            $updatedAddress = array(
                'name' => array(
                  'full' => $order->getCustomerFirstname()." ".$order->getCustomerLastname()
                ),
                'address' => array(
                    'line1' => $street[0],
                    'line2' => isset($street[1]) ? $street[1] : ' ',
                    'state' => $orderAddress->getRegion(),
                    'zipcode' => $orderAddress->getPostcode(),
                    'country' => $orderAddress->getCountry(),
                    'city' =>$orderAddress->getCity(),
                ),
            );
            Mage::getModel('affirm/payment')->orderUpdate($updatedAddress,$chargeId);
        }

    }
}
