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
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml paypal settlement reports grid block
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
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
        $confirmationMessage = Mage::helper('core')->jsQuoteEscape(
            Mage::helper('paypal')
                ->__('Connecting to PayPal SFTP server to fetch new reports. Are you sure you want to proceed?')
        );
        $this->_addButton('fetch', [
            'label'   => Mage::helper('paypal')->__('Fetch Updates'),
            'onclick' => "confirmSetLocation('{$confirmationMessage}', '{$this->getUrl('*/*/fetch')}')",
            'class'   => 'task'
        ]);
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
