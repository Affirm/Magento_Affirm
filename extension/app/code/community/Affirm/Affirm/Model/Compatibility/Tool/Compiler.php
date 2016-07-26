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
class Affirm_Affirm_Model_Compatibility_Tool_Compiler extends Affirm_Affirm_Model_Compatibility_Tool_Entity_Abstract
{
    /**#@+
     * Define constants
     */
    const REQUIRE_PATTERN = "/require(.*)OnepageController.php/";
    const CORRECT_REQUIRE_DECLARATION = 'Mage::getModuleDir';
    const CACHE_KEY = 'AFFIRM_AFFIRM_COMPILER';
    /**#@-*/

    /**
     * Is module enabled
     *
     * @param SplFileInfo $info
     * @return bool
     */
    protected function _isControllerModuleEnabled($info)
    {
        $codeDir = Mage::getBaseDir('code') . DS;
        $relativeFilePath = str_replace($codeDir, '', $info->getPathname());
        $from = strpos($relativeFilePath, DS);
        $to = strpos($relativeFilePath, 'controllers');
        $moduleData = substr($relativeFilePath, $from, $to - $from);
        $modName = str_replace(DS, '_', trim($moduleData, DS));
        return Mage::helper('core')->isModuleEnabled($modName);
    }

    /**
     * Check if skip controller file from specific module
     *
     * @param SplFileInfo $info
     * @param string $codePoolPath
     * @return int
     */
    protected function _ignoreFromSkippedModule($info, $codePoolPath)
    {
        $subPath = $codePoolPath . DS . str_replace('_', DS, $this->_getSkipModuleName());
        $isEnable = $this->_isControllerModuleEnabled($info, $subPath);

        return (strpos($info->getPathname(), $subPath) === 0) || !$isEnable;
    }

    /**
     * If type of controller
     *
     * @param SplFileInfo $info
     * @return int
     */
    protected function _isTypeOfController($info)
    {
        return preg_match("/Controller.php$/", $info->getFileName());
    }

    /**
     * Get controller list with bad OnepageController require statement
     *
     * @return array
     */
    public function getCompilerControllerList()
    {
        $cache = Mage::app()->loadCache(self::CACHE_KEY);
        if ($this->useCache() && $cache) {
            $controllersCompilerProblems = unserialize($cache);
        } else {
            $modulesPath = Mage::getBaseDir('code');
            $controllers = array();
            $controllersCompilerProblems = array();
            foreach ($this->_getNonCoreCodePools() as $codePool) {
                $codePoolPath = $modulesPath . DS . $codePool;
                if (file_exists($codePoolPath) && is_dir($codePoolPath)) {
                    $directory = new \RecursiveDirectoryIterator($modulesPath . DS . $codePool);
                    $iterator = new \RecursiveIteratorIterator($directory);
                    foreach ($iterator as $info) {
                        if ($info->isFile() && $this->_isTypeOfController($info) &&
                            !$this->_ignoreFromSkippedModule($info, $codePoolPath)
                        ) {
                            $controllers[] = $info->getPathname();
                        }
                    }
                }
            }
            foreach ($controllers as $controller) {
                $contents = file_get_contents($controller);
                if (preg_match_all(self::REQUIRE_PATTERN, $contents, $matches)) {
                    if (isset($matches[0][0]) &&
                        (strpos($matches[0][0], self::CORRECT_REQUIRE_DECLARATION) === false)
                    ) {
                        $controllersCompilerProblems[] = $controller;
                    }
                }
            }
            if ($this->useCache()) {
                Mage::app()->saveCache(serialize($controllersCompilerProblems),
                    self::CACHE_KEY, array(self::CACHE_TYPE)
                );
            }
        }
        return $controllersCompilerProblems;
    }
}
