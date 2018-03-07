<?php
class Affirm_Affirm_Block_Adminhtml_Grid_Renderer_Color extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row) {		
        $status =  $row->getData($this->getColumn()->getIndex());		
			if ($status == 1)			{     			
				$color = "10a900";
				$value = "Active";
			}
			else {
                $color = "ff031b";
				$value = "Inactive";
			}
		
        return '<div style="text-align:center; color:#FFF;font-weight:bold;background:#'.$color.';border-radius:8px;width:100%">'.$value.'</div>';
    }
}