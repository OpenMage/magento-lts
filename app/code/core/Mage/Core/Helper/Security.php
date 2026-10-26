<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 */
class Mage_Core_Helper_Security
{
    private $invalidBlockActions
        = [
            ['block' => Mage_Page_Block_Html_Topmenu_Renderer::class, 'method' => 'render'],
            ['block' => Mage_Core_Block_Template::class, 'method' => 'fetchView'],
        ];

    /**
     * @param  string              $method
     * @param  string[]            $args
     * @throws Mage_Core_Exception
     */
    public function validateAgainstBlockMethodBlacklist(Mage_Core_Block_Abstract $block, $method, array $args)
    {
        foreach ($this->invalidBlockActions as $action) {
            $calledMethod = strtolower($method);
            if (str_contains($calledMethod, '::')) {
                $calledMethod = explode('::', $calledMethod)[1];
            }

            if ($block instanceof $action['block'] && strtolower($action['method']) === $calledMethod) {
                Mage::throwException(
                    sprintf('Action with combination block %s and method %s is forbidden.', $block::class, $method),
                );
            }
        }
    }
}
