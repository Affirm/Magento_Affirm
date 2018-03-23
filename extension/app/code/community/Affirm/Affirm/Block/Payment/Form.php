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
class Affirm_Affirm_Block_Payment_Form extends Mage_Payment_Block_Form
{
    /**
     * Set custom template, customize label
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData('area','frontend');
        $this->setTemplate('affirm/affirm/payment/form/affirm.phtml');
        $this->_replaceLabel();
    }

    /**
     * Replaces default label with custom image, conditionally displaying text
     * based on the Affirm product.
     */
    protected function _replaceLabel()
    {
        if (!$this->helper('affirm')->isPlainTextEnabled()) {
            $this->setMethodTitle('');
            $html = $this->helper('affirm')->getLabelHtmlAfter();
            $this->setMethodLabelAfterHtml($html);
        }
    }
}
