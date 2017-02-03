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

//Add exclusively/inclusively attribute to category
try {
    $entity = 'catalog_category';
    $attributeCode = 'affirm_category_mfp_priority';
    $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode($entity, $attributeCode);
    $setup = Mage::getModel('eav/entity_setup', 'core_setup');

    if (!$attribute->getId()) {
        $setup->addAttribute(
            $entity, $attributeCode,
            array(
                'group'          => 'General Information',
                'type'           => 'int',
                'input'          => 'text',
                'label'          => 'Multiple Financing Program priority',
                'visible'        => 0,
                'required'       => 0,
                'user_defined'   => 1,
                'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                'default'        => 0,
                'frontend_class' => 'validate-digits'
            ));
    }

    //Add exclusively/inclusively attribute to product
    $entity = 'catalog_product';
    $attributeCode = 'affirm_product_mfp_priority';
    $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode($entity, $attributeCode);

    if (!$attribute->getId()) {
        $setup->addAttribute(
            $entity, $attributeCode,
            array(
                'group'          => 'General',
                'type'           => 'int',
                'input'          => 'text',
                'label'          => 'Multiple Financing Program priority',
                'backend'        => '',
                'visible'        => 0,
                'required'       => 0,
                'is_unique'      => 0,
                'comparable'     => 0,
                'user_defined'   => 1,
                'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                'default'        => 0,
                'frontend_class' => 'validate-digits'
            ));

        $setup->updateAttribute($entity, $attributeCode, 'is_visible_on_front', 0);
    }

} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
