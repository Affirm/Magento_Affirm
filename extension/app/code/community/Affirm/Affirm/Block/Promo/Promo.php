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
class Affirm_Affirm_Block_Promo_Promo extends Mage_Core_Block_Template
{
    /**
     * Helper
     *
     * @var Affirm_Affirm_Helper_Promo_Data
     */
    protected $_helper;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_helper = Mage::helper('affirm/promo_data');
        parent::_construct();
    }

    /**
     * Get snippet code
     *
     * @return string
     */
    /**
     * Get html code for promo snippet
     *
     * @return string
     */
    public function getSnippetCode()
    {
        if (!$this->_helper->isPromoActive() || !$this->_helper->getSectionConfig()->getDisplay()) {
            return '';
        }

        $sectionConfig = $this->_helper->getSectionConfig();
        $container = $sectionConfig->getContainer();
        $snippet = $this->getChildHtml('affirmpromo_snippet');

        if (!empty($container)) {
            $snippet = str_replace('{container}', $snippet, $container);
        }
        return $snippet;
    }
}
