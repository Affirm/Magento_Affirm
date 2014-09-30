<?php

class Affirm_Affirm_Helper_Data extends Mage_Core_Helper_Abstract
{

  public function getAffirmJsUrl()
  {
        $api_url = Mage::getStoreConfig('payment/affirm/api_url');
        $domain = parse_url($api_url, PHP_URL_HOST);
        $domain = str_ireplace('www.', '', $domain);
        $prefix = 'cdn1.';
        if (strpos($domain, 'sandbox') === 0) {
            $prefix = 'cdn1-';
        }
        return 'https://' . $prefix . '' . $domain . '/js/v2/affirm.js';

  }

}
