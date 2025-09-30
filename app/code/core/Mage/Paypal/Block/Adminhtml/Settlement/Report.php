<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */


/**
 * Adminhtml paypal settlement reports grid block
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_Settlement_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'paypal';
        $this->_controller = 'adminhtml_settlement_report';
        $this->_headerText = Mage::helper('paypal')->__('PayPal Settlement Reports');
        parent::__construct();
        $this->_removeButton('add');
        $this->_addButton('fetch', [
            'label'   => Mage::helper('paypal')->__('Fetch Updates'),
            'onclick' => Mage::helper('core/js')->getConfirmSetLocationJs(
                $this->getUrl('*/*/fetch'),
                Mage::helper('paypal')->__('Connecting to PayPal SFTP server to fetch new reports. Are you sure you want to proceed?'),
            ),
            'class'   => 'task',
        ]);
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
