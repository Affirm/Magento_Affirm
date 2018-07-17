<?php
/**
 *
 *  * BSD 3-Clause License
 *  *
 *  * Copyright (c) 2018, Affirm
 *  * All rights reserved.
 *  *
 *  * Redistribution and use in source and binary forms, with or without
 *  * modification, are permitted provided that the following conditions are met:
 *  *
 *  *  Redistributions of source code must retain the above copyright notice, this
 *  *   list of conditions and the following disclaimer.
 *  *
 *  *  Redistributions in binary form must reproduce the above copyright notice,
 *  *   this list of conditions and the following disclaimer in the documentation
 *  *   and/or other materials provided with the distribution.
 *  *
 *  *  Neither the name of the copyright holder nor the names of its
 *  *   contributors may be used to endorse or promote products derived from
 *  *   this software without specific prior written permission.
 *  *
 *  * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 *  * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 *  * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 *  * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 *  * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 *  * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 *  * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 *  * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 *  * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

/**
 * Affirm order tracking confirm pixel
 */
class Affirm_Affirm_Block_Promo_Pixel_Confirm extends Mage_Core_Block_Template
{

    public function getOrdersTrackingCodeAffirmAnalytics()
    {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => $orderIds));
        $result = array();
        foreach ($collection as $order) {
            $result[] = sprintf("affirm.analytics.trackOrderConfirmed({
'affiliation': '%s',
'orderId': '%s',
'currency': '%s',
'total': '%s',
'tax': '%s',
'shipping': '%s',
'paymentMethod': '%s'
});",
                $this->jsQuoteEscape(Mage::app()->getStore()->getFrontendName()),
                $order->getIncrementId(),
                $order->getOrderCurrencyCode(),
                Mage::helper('affirm/util')->formatCents($order->getBaseGrandTotal()),
                Mage::helper('affirm/util')->formatCents($order->getBaseTaxAmount()),
                Mage::helper('affirm/util')->formatCents($order->getBaseShippingAmount()),
                $order->getPayment()->getMethod()
            );
        }
        return implode("\n", $result);
    }

    /**
     * Is ga available
     *
     * @return bool
     */
    protected function _isAvailable()
    {
        return Mage::helper('affirm/promo_data')->isConfirmPixelEnabled();
    }

    /**
     * Render Affirm tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isAvailable()) {
            return '';
        }
        return parent::_toHtml();
    }

    public function getCustomerSessionId()
    {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => $orderIds));
        $result = array();
        foreach ($collection as $order) {
            $customerId = ($order->getCustomerId()) ? $order->getCustomerId() : $guestId = "CUSTOMER-" . Mage::helper('affirm/promo_data')->getDateMicrotime();
        }
        return $customerId;
    }
}
