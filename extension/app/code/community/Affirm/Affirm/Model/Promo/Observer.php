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
class Affirm_Affirm_Model_Promo_Observer
{
    /**
     * Is need to change left reference for 'rwd' theme
     *
     * @param Varien_Object $config
     * @return bool
     */
    protected function _isChangeLeftBlockReference($config)
    {
        return $config->getPositionHorizontal() == 'left' && $config->getPositionVertical() == 'top' &&
            Mage::getSingleton('core/design_package')->getPackageName() == 'rwd' &&
            $config->getPageCode() == 'catalog_category';
    }

    /**
     * Update page layout, add banner code.
     *
     * @param Varien_Event_Observer $observer
     * @return Affirm_Affirm_Model_Promo_Observer
     */
    public function layoutGenerateBlocksBefore(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('affirm/promo_data')->isPromoActive() || Mage::app()->getStore()->isAdmin()) {
            return '';
        }
        $config = Mage::helper('affirm/promo_data')->getSectionConfig();
        if ($config->getDisplay()) {
            switch ($config->getPositionHorizontal()) {
                case 'left': $name = 'left'; break;
                case 'right': $name = 'right'; break;
                default: $name = 'content'; break;
            }
            if ($this->_isChangeLeftBlockReference($config)) {
                    $name = 'left_first';
            }
            $vertical = ('top' == $config->getPositionVertical() ? 'before="-"' : 'after="+"');
            $layout = $observer->getEvent()->getLayout();
            $layout->getUpdate()->addUpdate(
                '<reference name="' . $name . '">
                    <block type="affirm/promo_promo" name="affirmpromo"
                        template="affirm/promo/promo.phtml" ' . $vertical . '>
                        <block type="core/template" name="affirmpromo_snippet"
                            template="affirm/promo/snippet.phtml">
                        </block>
                    </block>
                </reference>');
        }
        return $this;
    }
}

