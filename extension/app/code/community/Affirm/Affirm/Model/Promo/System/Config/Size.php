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
class Affirm_Affirm_Model_Promo_System_Config_Size extends Varien_Object
{
    /**
     * Sizes
     *
     * @var array
     */
    public static $sizes = array(
        '120x90',
        '150x100',
        '170x100',
        '190x100',
        '234x60',
        '300x50',
        '468x60',
        '300x250',
        '336x280',
        '540x200',
        '728x90',
        '800x66',
        '250x250',
        '280x280',
        '120x240',
        '120x600',
        '234x400'
    );

    /**
     * Get options array for configuration field
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach (self::$sizes  as $size) {
            $options[] = array('value' => $size, 'label' => $size);
        }
        return $options;
    }
}
