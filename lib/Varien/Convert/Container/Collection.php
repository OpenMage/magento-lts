<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Convert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert component collection
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Varien_Convert_Container_Collection
{
    protected $_items = [];
    protected $_defaultClass = 'Varien_Convert_Container_Generic';

    public function setDefaultClass($className)
    {
        $this->_defaultClass = $className;
        return $this;
    }

    public function addItem($name, Varien_Convert_Container_Interface $item)
    {
        if (is_null($name)) {
            if ($item->getName()) {
                $name = $item->getName();
            } else {
                $name = count($this->_items);
            }
        }

        $this->_items[$name] = $item;

        return $item;
    }

    public function getItem($name)
    {
        if (!isset($this->_items[$name])) {
            $this->addItem($name, new $this->_defaultClass());
        }
        return $this->_items[$name];
    }

    public function hasItem($name)
    {
        return isset($this->_items[$name]);
    }
}
