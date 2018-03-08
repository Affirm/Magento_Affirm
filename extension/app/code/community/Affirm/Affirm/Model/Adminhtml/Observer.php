<?php
class Affirm_Affirm_Model_Adminhtml_Observer
{
    /**
     * Predispatch action controller
     */
    public function preDispatch()
    {
        Mage::log(__METHOD__);
        /* @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');
        if ($session->isLoggedIn()) {
            $feedModel = Mage::getModel(
                'affirm/feed'
            );
            /* @var $feedModel Mage_AdminNotification_Model_Feed */
            $feedModel->checkUpdate();
        }
    }
    
}