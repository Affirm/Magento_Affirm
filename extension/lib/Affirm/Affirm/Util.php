<?php

class Affirm_Util {

    //  .2   -  right precision
    //  f    -  as float
    const MONEY_FORMAT = "%.2f";

    public static function formatMoney($amount) {
        return sprintf(Affirm_Util::MONEY_FORMAT, $amount);
    }

    public static function formatCents($amount = 0) {
        $negative = false;

        $str = Affirm_Util::formatMoney($amount);

        if (strcmp($str[0], "-") === 0) {
            // treat it like a positive. then prepend a '-' to the return value.
            $str = substr($str, 1);
            $negative = true;
        }

        $parts = explode(".", $str, 2);
        if ($parts === false) {
            return 0;
        }

        if (empty($parts)) {
            return 0;
        }

        if (strcmp($parts[0], 0) === 0 && strcmp($parts[1], "00") === 0) {
            return 0;
        }

        $retVal = "";
        if ($negative) {
            $retVal .= "-";
        }
        $retVal .= ltrim( $parts[0] . substr($parts[1], 0, 2), "0");
        return intval($retVal);
    }

}
