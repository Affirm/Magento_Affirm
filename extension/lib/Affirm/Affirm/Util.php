<?php

class Affirm_Util {
    public static function formatCents($amount = 0) {
        return (int) ($amount * 100);
    }
}
