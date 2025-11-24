<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

/**
 * Convert factory
 *
 * @package    Varien_Convert
 */
class Varien_Convert
{
    public static function convert($class, $method, $data, array $vars = [])
    {
        if (is_string($class)) {
            $class = new $class();
        }

        $action = new Varien_Convert_Action();
        $action->setParam('method', $method)->setParam('class', $class);

        $container = $action->getContainer();
        $container->setData($data)->setVars($vars);

        $action->run();
        return $action->getData();
    }
}
