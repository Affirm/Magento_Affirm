<?php

class Affirm_AffirmPromo_Model_System_Config_Position extends Varien_Object
{
    public static $positions = array(
        "↖ left-top" => "left-top",
        "↙ left-bottom" => "left-bottom",
        "↑ center-top" => "center-top",
        "↓ center-bottom" => "center-bottom",
        "↗ right-top" => "right-top",
        "↘ right-bottom" => "right-bottom"
    );

    /**
     * Get options array for configuration field
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach(self::$positions as $label => $position) {
            $options[] = array('label' => $label, 'value' => $position);
        }
        return $options;
    }
}
