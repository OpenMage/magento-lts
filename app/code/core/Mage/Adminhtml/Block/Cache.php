<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cache extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_controller = 'cache';
        $this->_headerText = Mage::helper('core')->__('Cache Storage Management');
        parent::__construct();
        $this->_removeButton('add');
        $this->_addButton('flush_magento', [
            'label'     => Mage::helper('core')->__('Flush & Apply Updates'),
            'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getFlushSystemUrl()),
            'class'     => 'delete cache',
        ]);

        $this->_addButton('flush_system', [
            'label'     => Mage::helper('core')->__('Flush Cache Storage'),
            'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs(
                $this->getFlushStorageUrl(),
                Mage::helper('core')->__('Cache storage may contain additional data. Are you sure that you want flush it?'),
            ),
            'class'     => 'delete flush',
        ]);
    }

    /**
     * Get url for clean cache storage
     *
     * @return string
     */
    public function getFlushStorageUrl()
    {
        return $this->getUrl('*/*/flushAll');
    }

    /**
     * Get url for clean cache storage
     *
     * @return string
     */
    public function getFlushSystemUrl()
    {
        return $this->getUrl('*/*/flushSystem');
    }
}
