<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml accordion widget
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Accordion extends Mage_Adminhtml_Block_Widget
{
    protected $_items = [];
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('widget/accordion.phtml');
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function addItem($itemId, $config)
    {
        $this->_items[$itemId] = $this->getLayout()->createBlock('adminhtml/widget_accordion_item')
            ->setData($config)
            ->setAccordion($this)
            ->setId($itemId);
        if (isset($config['content']) && $config['content'] instanceof Mage_Core_Block_Abstract) {
            $this->_items[$itemId]->setChild($itemId . '_content', $config['content']);
        }

        $this->setChild($itemId, $this->_items[$itemId]);
        return $this;
    }
}
