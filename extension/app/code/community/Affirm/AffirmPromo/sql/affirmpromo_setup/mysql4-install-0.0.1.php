<?php

$installer = $this;
$installer->startSetup();

$adminSession = Mage::getSingleton('admin/session');
$adminSession->unsetAll();
$adminSession->getCookie()->delete($adminSession->getSessionName());

$installer->endSetup();
