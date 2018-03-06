<?php

class Affirm_Affirm_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    const XML_FEEDS_PATH        = 'payment/affirm/notification_feed';
    const XML_FREQUENCY_PATH    = 'payment/affirm/notification_check_frequency';
    const XML_LAST_UPDATE_PATH  = 'payment/affirm/notification_last_update';
    const XML_FEEDS_SUBSCRIBED  = 'payment/affirm/notification_update';

    protected function _isNotificationSubscribed()
    {
        return Mage::getStoreConfig(self::XML_FEEDS_SUBSCRIBED) == 1;
    }

    public function checkUpdate()
    {
        if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }


        if (!extension_loaded('curl')) {
            return $this;
        }

        /* @var $inbox Mage_AdminNotification_Model_Inbox */
        $inbox = Mage::getModel('adminnotification/inbox');
        if ($this->_isNotificationSubscribed()) {
            $feed = Mage::getStoreConfig(self::XML_FEEDS_PATH);
            $this->_feedUrl = $feed;
            $feedData = array();

            $feedXml = $this->getFeedData($feed);
            if ($feedXml && $feedXml->entry) {
                foreach ($feedXml->entry as $item) {
                    $feedData[] = array(
                        'severity' =>
                            (int)isset($item->severity) ? $item->severity
                                : Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE,
                        'date_added' => $this->getDate((string)$item->updated),
                        'title' => 'Affirm Extension Version '.(string)$item->title.' is now available',
                        'description' => 'Affirm Extension Version '.(string)$item->title.' for Magento is now available for download and upgrade. To see a full list of updates please check the release notes page.',
                        'url' => 'https://github.com/Affirm/Magento_Affirm/releases', // Affirm Magento 1 extension github releases
                    );
                    break; // we only need latest release version notification
                }
                if ($feedData) {
                    $inbox->parse(array_reverse($feedData));
                }
            }
            $this->setLastUpdate();
        }

        return $this;
    }

    /**
     * Retrieve Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return Mage::app()->loadCache('affirm_admin_notifications');
    }

    /**
     * Set last update time (now)
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'affirm_admin_notifications');
        return $this;
    }

    /**
     * Retrieve feed data as XML element
     * Changed for following redirects
     *
     * @return SimpleXMLElement
     */
    public function getFeedData()
    {
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array(
            'timeout'   => 2
        ));
        $curl->setOptions(array(
            CURLOPT_FOLLOWLOCATION => true,
        ));

        $curl->write(Zend_Http_Client::GET, $this->getFeedUrl(), '1.0');
        $data = $curl->read();
        if ($data === false) {
            return false;
        }
        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);
        $curl->close();

        try {
            $xml  = new SimpleXMLElement($data);
        }
        catch (Exception $e) {
            return false;
        }

        return $xml;
    }

    public function getFrequency()
    {
        return Mage::getStoreConfig(self::XML_FREQUENCY_PATH);
    }
}
