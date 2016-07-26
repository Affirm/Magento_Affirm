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
class Affirm_Affirm_Adminhtml_Compatibility_ToolController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Tools'))->_title($this->__('Affirm Compatibility Tool'));

        $this->loadLayout();
        $this->_setActiveMenu('system/tools');
        $this->renderLayout();
    }

    /**
     * Validate
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        try {
            $validationEventResult = $this->getLayout()
                ->createBlock('affirm/adminhtml_compatibility_tool_event')->toHtml();
            $response->setValidationEventResult($validationEventResult);

            $validationClassResult = $this->getLayout()
                ->createBlock('affirm/adminhtml_compatibility_tool_classRewrites')->toHtml();
            $response->setValidationClassResult($validationClassResult);

            $validationCompilerResult = $this->getLayout()
                ->createBlock('affirm/adminhtml_compatibility_tool_compiler')->toHtml();
            $response->setValidationCompilerResult($validationCompilerResult);
            $response->setSuccess(true);
        } catch (Exception $e) {
            Mage::logException($e);
            $response->setSuccess(false);
        };
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Is controller allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/tools/compatibility');
    }
}
