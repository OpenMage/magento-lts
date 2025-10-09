<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal Debug Grid Container Block
 *
 * Provides the container for the PayPal debug log grid in the admin panel
 */
class Mage_Paypal_Block_Adminhtml_Debug extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize debug grid container
     *
     * Sets up the grid container properties, including block group, controller,
     * header text, and configures available buttons.
     */
    public function __construct()
    {
        $this->_blockGroup = 'paypal';
        $this->_controller = 'adminhtml_debug';
        $this->_headerText = Mage::helper('paypal')->__('PayPal Debug Log');
        parent::__construct();
        $this->_removeButton('add');

        $this->_addButton('delete_all', [
            'label'   => Mage::helper('paypal')->__('Delete All Logs'),
            'onclick' => 'deleteConfirm(\'' .
                Mage::helper('paypal')->__('Are you sure you want to delete all debug logs?') .
                '\', \'' . $this->getUrl('*/*/deleteAll') . '\')',
            'class'   => 'delete',
        ]);
    }
}
