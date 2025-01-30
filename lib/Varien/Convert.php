<?php

/**
 * @category   Varien
 * @package    Varien_Convert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Convert factory
 *
 * @category   Varien
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
