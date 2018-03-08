<?php
class Affirm_Affirm_Model_Mysql4_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('affirm/rule');
    }
    
    public function addAddressFilter($address)
    {
        $this->addFieldToFilter('is_active', 1);
        
        $storeId = $address->getQuote()->getStoreId();
        $storeId = intVal($storeId);
        $this->getSelect()->where('stores="" OR stores LIKE "%,'.$storeId.',%"');
        
        $groupId = 0;
        if ($address->getQuote()->getCustomerId()){
            $groupId = $address->getQuote()->getCustomer()->getGroupId();    
        }
        $groupId = intVal($groupId);
        $this->getSelect()->where('cust_groups="" OR cust_groups LIKE "%,'.$groupId.',%"');
        
        return $this;
    }    
}