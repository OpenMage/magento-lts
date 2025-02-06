<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
     * @param string                   $method
     * @param string[]                 $args
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
                    sprintf('Action with combination block %s and method %s is forbidden.', get_class($block), $method),
                );
            }
        }
    }
}
