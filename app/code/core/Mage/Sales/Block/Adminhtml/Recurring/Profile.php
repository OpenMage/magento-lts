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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Instructions to create child grid
     *
     * @var string
     */
    protected $_blockGroup = 'sales';
    protected $_controller = 'adminhtml_recurring_profile';

    /**
     * Set header text and remove "add" btn
     */
    public function __construct()
    {
        $this->_headerText = Mage::helper('sales')->__('Recurring Profiles (beta)');
        parent::__construct();
        $this->_removeButton('add');
    }
}
