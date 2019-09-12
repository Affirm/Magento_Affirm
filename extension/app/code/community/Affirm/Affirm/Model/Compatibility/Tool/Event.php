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
class Affirm_Affirm_Model_Compatibility_Tool_Event
    extends Affirm_Affirm_Model_Compatibility_Tool_Entity_Abstract
{
    /**#@+
     * Define constants
     */
    const CONFLICT_TYPE = 'event';
    const CACHE_KEY = 'AFFIRM_AFFIRM_COMPATIBILITY_EVENT';
    /**#@-*/

    /**
     * Get events
     *
     * @return array
     */
    public function getObserverAffirmEvents()
    {
        return array(
            'checkout_type_onepage_save_order',
            'checkout_type_onepage_save_order_after'
        );
    }

    /**
     * Identify conflict with separate module
     *
     * @param string $modName
     * @return array
     */
    protected function _getConflictWithModule($modName)
    {
        $result = array();
        $moduleConfig = $this->_getModuleConfig($modName);

        $searchAreas = $this->_getSearchAreas();
        $observerAffirmEvent = $this->getObserverAffirmEvents();
        foreach ($searchAreas as $area) {
            $globalEventObservers = $moduleConfig->getNode()->{$area}->{self::CONFLICT_TYPE . 's'};
            $globalEventObservers = ($globalEventObservers) ? $globalEventObservers->asArray() : '';
            if (!empty($globalEventObservers)) {
                foreach ($globalEventObservers as $observer => $observerOptions) {
                    if (in_array($observer, $observerAffirmEvent)) {
                        foreach ($observerOptions['observers'] as $option) {
                            $result[$observer][] = $option;
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Get the same observer declaration
     *
     * @return array
     */
    public function getObserverDeclarationDuplicate()
    {
        $cache = Mage::app()->loadCache(self::CACHE_KEY);
        if ($this->useCache() && $cache) {
            $declarationConflicts = unserialize($cache);
        } else {
            $declarationConflicts = array();
            $modules = (array)Mage::getConfig()->getNode('modules')->children();

            foreach ($modules as $modName => $module) {
                if ($this->_skipValidation($modName, $module)) {
                    continue;
                }
                $result = $this->_getConflictWithModule($modName);
                if (!empty($result)) {
                    $declarationConflicts = $result;
                }
            }
            if ($this->useCache()) {
                Mage::app()->saveCache(serialize($declarationConflicts), self::CACHE_KEY, array(self::CACHE_TYPE));
            }
        }
        return $declarationConflicts;
    }
}

