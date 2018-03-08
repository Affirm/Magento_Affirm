<?php
class Affirm_Affirm_Model_Adminhtml_Observer
{
    /**
     * Predispatch action controller
     * @param Varien_Event_Observer $observer
     */
    public function preDispatch(Varien_Event_Observer $observer)
    {
        /* @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');
        if ($session->isLoggedIn()) {
            $feedModel = Mage::getModel('affirm/feed');
            /* @var $feedModel Mage_AdminNotification_Model_Feed */
            $feedModel->checkUpdate();
        }
    }

}