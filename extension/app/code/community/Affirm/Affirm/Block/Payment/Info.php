<?php
class Affirm_Affirm_Block_Payment_Info extends Mage_Payment_Block_Info_Cc
{
    /**
     * Don't show CC type for non-CC methods
     *
     * @return string|null
     */
    public function getCcTypeName()
    {
    }

    /**
     * Prepare PayPal-specific payment information
     *
     * TODO(brian): modify this to get rid of "Credit Card Type" and "Credit Card Number"
     * fields
     *
     * @param Varien_Object|array $transport
     * return Varien_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        //$payment = $this->getInfo();
        $info = array("method"=>"Payment via affirm website");
        return $transport->addData($info);
    }
}
