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
class Affirm_Affirm_Block_Adminhtml_Compatibility_Tool_ClassRewrites extends Mage_Core_Block_Template
{
    /**
     * Set custom template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('affirm/compatibility/tool/class-rewrites.phtml');
    }

    /**
     * Get xml declaration rewrites
     *
     * @return array
     */
    public function getXmlClassRewrites()
    {
        return Mage::getModel('affirm/compatibility_tool_classRewrites')->getXmlClassRewrites();
    }

    /**
     * Get code pool declaration rewrites
     *
     * @return array
     */
    public function getCodePoolClassRewrite()
    {
        return Mage::getModel('affirm/compatibility_tool_classRewrites')->getCodePoolClassRewrite();
    }
}
