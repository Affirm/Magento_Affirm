<?php
class Affirm_Affirm_Block_Adminhtml_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_rule';
        $this->_blockGroup = 'affirm';
        $this->_headerText = Mage::helper('affirm')->__('Payment Restriction Rules');
        $this->_addButtonLabel = Mage::helper('affirm')->__('Add Rule');
        parent::__construct();
    }
}