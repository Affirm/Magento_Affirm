<?php
class Affirm_Affirm_Block_Adminhtml_Rule_Grid_Renderer_Groups extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        /* @var $hlp Affirm_Affirm_Helper_Data */
        $hlp = Mage::helper('affirm');
        
        $groups = $row->getData('cust_groups');
        if (!$groups) {
            return $hlp->__('Restricts For All');
        }
        $groups = explode(',', $groups);
        
        $html = '';
        foreach($hlp->getAllGroups() as $row)
        {
            if (in_array($row['value'], $groups)){
                $html .= $row['label'] . "<br />";
            }
        }
        return $html;
    }
}