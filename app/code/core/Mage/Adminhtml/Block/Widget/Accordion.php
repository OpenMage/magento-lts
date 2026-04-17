<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml accordion widget
 *
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
