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

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

try {
    $setup = Mage::getModel('eav/entity_setup', 'core_setup');

    $setup->run("
        drop table if exists {$this->getTable('affirm/rule')};
        CREATE TABLE `{$this->getTable('affirm/rule')}` (
          `rule_id`     mediumint(8) unsigned NOT NULL auto_increment,
          `for_admin`   tinyint(1) unsigned NOT NULL default '0',
          `is_active`   tinyint(1) unsigned NOT NULL default '0',
          `all_stores`  tinyint(1) unsigned NOT NULL default '0',
          `all_groups`  tinyint(1) unsigned NOT NULL default '0',
          `name`        varchar(255) default '', 
          `stores`      varchar(255) NOT NULL default '', 
          `cust_groups` varchar(255) NOT NULL default '', 
          `message`     varchar(255) default '', 
          `methods`     text, 
          `conditions_serialized`   text, 
          PRIMARY KEY  (`rule_id`)  
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
        drop table if exists {$this->getTable('affirm/attribute')};
        CREATE TABLE `{$this->getTable('affirm/attribute')}` (
          `attr_id` mediumint(8) unsigned NOT NULL auto_increment,
          `rule_id` mediumint(8) unsigned NOT NULL,
          `code`    varchar(255) NOT NULL default '',
          PRIMARY KEY  (`attr_id`),
          CONSTRAINT `FK_AFFIRM_RULE` FOREIGN KEY (`rule_id`) REFERENCES {$this->getTable('affirm/rule')} (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE 
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
        ");
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
?>

