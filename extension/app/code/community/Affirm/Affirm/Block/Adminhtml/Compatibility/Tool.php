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
class Affirm_Affirm_Block_Adminhtml_Compatibility_Tool extends Mage_Adminhtml_Block_Template
{
    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('run_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                        'label'   => $this->helper('affirm')->__('Validate Affirm Compatibility'),
                        'onclick' => 'AFFIRM_AFFIRM.tool.validateCompatibility(\'' .  $this->getAjaxUrl() . '\');',
                        'class'  => 'save'
                    ))
        );
        return parent::_prepareLayout();
    }

    /**
     * Get page header text
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->helper('affirm')->__('Affirm Compatibility Tool');
    }

    /**
     * Get ajax validate url
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('adminhtml/compatibility_tool/validate',
            array('_secure' => Mage::app()->getStore()->isCurrentlySecure()));
    }

    /**
     * Get html code of rum button
     *
     * @return string
     */
    public function getRunButtonHtml()
    {
        return $this->getChildHtml('run_button');
    }
}
