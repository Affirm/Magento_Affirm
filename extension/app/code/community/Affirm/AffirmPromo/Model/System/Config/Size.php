<?php

class Affirm_AffirmPromo_Model_System_Config_Size extends Varien_Object
{
    public static $sizes = array(
        "196x193",
        "560x63"
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
