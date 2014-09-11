<?php

class Affirm_AffirmPromo_Model_System_Config_Size extends Varien_Object
{
    public static $sizes = array(
        "196x193",
        "560x63",
        "120x90",
        "150x100",
        "170x100",
        "190x100",
        "234x60",
        "300x50",
        "468x60",
        "300x250",
        "336x280",
        "540x200",
        "728x90",
        "800x66",
        "250x250",
        "280x280",
        "120x240",
        "120x600",
        "234x400"
    );

    /**
     * Get options array for configuration field
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach(self::$sizes  as $size) {
            $options[] = array('value' => $size, 'label' => $size);
        }
        return $options;
    }
}
