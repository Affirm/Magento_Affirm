<?php
class Affirm_Affirm_Model_Rule_Observer
{
    /**
     * Append rule product attributes to select by quote item collection
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_SalesRule_Model_Observer
     */
    public function addProductAttributes(Varien_Event_Observer $observer)
    {
        // @var Varien_Object
        $attributesTransfer = $observer->getEvent()->getAttributes();

        $attributes = Mage::getResourceModel('affirm/rule')->getAttributes();
        
        $result = array();
        foreach ($attributes as $code) {
            $result[$code] = true;
        }
        $attributesTransfer->addData($result);
        
        return $this;
    }       
    
}