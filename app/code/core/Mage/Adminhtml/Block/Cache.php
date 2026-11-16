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
        $this->_removeButton(self::BUTTON_TYPE_ADD);

        $this->_addPreparedButton(
            id: 'flush_magento',
            label: Mage::helper('core')->__('Flush & Apply Updates'),
            class: 'delete cache',
            onClickUrl: $this->getFlushSystemUrl(),
        );

        $onClick = Mage::helper('core/js')->getConfirmSetLocationJs(
            $this->getFlushStorageUrl(),
            Mage::helper('core')->__('Are you sure you want to flush cache storage?'),
        );

        $this->_addPreparedButton(
            id: 'flush_system',
            label: Mage::helper('core')->__('Flush Cache Storage'),
            class: 'delete flush',
            onClick: $onClick,
        );
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
