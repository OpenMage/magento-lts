<?php

class Mage_Core_Helper_Security
{

    private $invalidBlockActions
        = [
            // explicitly not using class constant here Mage_Page_Block_Html_Topmenu_Renderer::class
            // if the class does not exists it breaks.
            ['block' => Mage_Page_Block_Html_Topmenu_Renderer::class, 'method' => 'render'],
            ['block' => Mage_Core_Block_Template::class, 'method' => 'fetchView'],
        ];

    /**
     * @param Mage_Core_Block_Abstract $block
     * @param string                   $method
     * @param string[]                 $args
     *
     * @throws Mage_Core_Exception
     */
    public function validateAgainstBlockMethodBlacklist(Mage_Core_Block_Abstract $block, $method, array $args)
    {
        foreach ($this->invalidBlockActions as $action) {
            $calledMethod = strtolower($method);
            if (($block instanceof $action['block'] && strtolower($action['method']) === $calledMethod)
                || ($block instanceof $action['block']
                    && strtolower($action['block'] . '::' . $action['method']) === $calledMethod)) {
                Mage::throwException(
                    sprintf('Action with combination block %s and method %s is forbidden.', get_class($block), $method)
                );
            }
        }
    }
}
