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
abstract class Affirm_Affirm_Model_Compatibility_Tool_Entity_Abstract extends Varien_Object
{
    /**#@+
     * Define constants
     */
    const USE_CACHE = false;
    const CACHE_TYPE = 'config';
    /**#@-*/

    /**
     * Check if use cache
     *
     * @return bool
     */
    public function useCache()
    {
        return Mage::app()->useCache(self::CACHE_TYPE) && self::USE_CACHE;
    }

    /**
     * Get non-core code pool
     *
     * @return array
     */
    protected function _getNonCoreCodePools()
    {
        return array('local', 'community');
    }

    /**
     * Get search area
     *
     * @return array
     */
    protected function _getSearchAreas()
    {
        return array('global', 'frontend');
    }

    /**
     * Skip affirm module
     *
     * @return string
     */
    protected function _getSkipModuleName()
    {
        return 'Affirm_Affirm';
    }

    /**
     * Get core package alias
     *
     * @return array
     */
    protected function _getCorePackagesAlias()
    {
        return array('Mage_', 'Enterprise_');
    }

    /**
     * Get all modules
     *
     * @return array
     */
    protected function _getAllModules()
    {
        return (array)Mage::getConfig()->getNode('modules')->children();
    }

    /**
     * Check if need to skip validation
     *
     * @param string $modName
     * @param Mage_Code_Model_Config_Element $module
     * @return bool
     */
    protected function _skipValidation($modName, $module)
    {
        return !$module->is('active') || !in_array((string)$module->codePool, $this->_getNonCoreCodePools())
            || ($modName == $this->_getSkipModuleName());
    }

    /**
     * Get module config options
     *
     * @param string $modName
     * @return Mage_Core_Model_Config_Base
     */
    protected function _getModuleConfig($modName)
    {
        $configFile = Mage::getConfig()->getModuleDir('etc', $modName) . DS . 'config.xml';
        $moduleConfig = Mage::getModel('core/config_base');
        $moduleConfig->loadString('<config/>');
        $moduleConfigBase = Mage::getModel('core/config_base');
        $moduleConfigBase->loadFile($configFile);
        $moduleConfig->extend($moduleConfigBase, true);
        return $moduleConfig;
    }
}
