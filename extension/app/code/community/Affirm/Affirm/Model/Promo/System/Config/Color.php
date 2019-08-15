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
 * Class Affirm_Affirm_Model_Promo_System_Config_Position
 */
class Affirm_Affirm_Model_Promo_System_Config_Color extends Varien_Object
{
    /**
     * Position options
     *
     * @var array
     */
    public static $colors = array(
        'Default'=> '' ,
        'Black'=> 'black',
        'White' => 'white'
    );

    /**
     * Get options array for configuration field
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach (self::$colors  as $color => $value) {
            $options[] = array('value' => $value, 'label' => $color);
        }
        return $options;
    }
}
