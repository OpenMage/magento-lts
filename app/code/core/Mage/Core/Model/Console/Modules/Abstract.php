<?php

abstract class Mage_Core_Model_Console_Modules_Abstract extends Mage_Console_Model_Command
{
    /**
     * Get modules list
     *
     * @return array
     */
    abstract protected function getModules(): array;

    /**
     * @return Mage_Core_Helper_Config
     */
    protected function getConfigHelper(): Mage_Core_Helper_Config
    {
        return Mage::helper('core/config');
    }

    /**
     * @param string $module
     * @param bool $active
     * @return void
     */
    protected function toggleModuleActive(string $module, bool $active)
    {
        if (array_key_exists($module, $this->getModules())) {
            $config = simplexml_load_file("app/etc/modules/{$module}.xml");
            $config->modules->{$module}->active = $active ? 'true' : 'false';
            $config->asXML("app/etc/modules/{$module}.xml");
        }
    }
}
