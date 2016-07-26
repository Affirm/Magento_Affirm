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
 * Class Affirm_Util
 */
class Affirm_Affirm_Helper_Util extends Mage_Core_Helper_Abstract
{
    /**
     * Money format
     */
    const MONEY_FORMAT = "%.2f";

    /**
     * Format money
     *
     * @param string $amount
     * @return string
     */
    protected function _formatMoney($amount)
    {
        return sprintf(self::MONEY_FORMAT, $amount);
    }

    /**
     * Format cents
     *
     * @param int $amount
     * @return int
     */
    public function formatCents($amount = 0)
    {
        $negative = false;
        $str = $this->_formatMoney($amount);
        if (strcmp($str[0], '-') === 0) {
            // treat it like a positive. then prepend a '-' to the return value.
            $str = substr($str, 1);
            $negative = true;
        }

        $parts = explode('.', $str, 2);
        if (($parts === false) || empty($parts)) {
            return 0;
        }

        if ((strcmp($parts[0], 0) === 0) && (strcmp($parts[1], '00') === 0)) {
            return 0;
        }

        $retVal = '';
        if ($negative) {
            $retVal .= '-';
        }
        $retVal .= ltrim($parts[0] . substr($parts[1], 0, 2), '0');
        return intval($retVal);
    }
}
