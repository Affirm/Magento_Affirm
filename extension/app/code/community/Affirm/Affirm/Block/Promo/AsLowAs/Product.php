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

/**
 * Affirm As Low As pdp link
 */
class Affirm_Affirm_Block_Promo_AsLowAs_Product extends Mage_Core_Block_Template
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->helper('affirm/promo_asLowAs')->isAsLowAsDisabledOnPDP()) {
            return "";
        }
        return parent::_toHtml();
    }

    /**
     * Get product on PDP
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        return $this->helper('catalog')->getProduct();
    }

    /**
     * Get final price
     *
     * @return int
     */
    public function getFinalPrice()
    {
        $price = $this->getProduct()->getFinalPrice();
        return $this->helper('affirm/util')->formatCents($price);
    }
}
