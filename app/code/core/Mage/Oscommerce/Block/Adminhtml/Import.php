<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce convert list block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Oscommerce_Block_Adminhtml_Import extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'oscommerce';
    public function __construct()
    {
        $this->_controller = 'adminhtml_import';
        $this->_headerText = Mage::helper('adminhtml')->__('Manage osCommerce Profiles');
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add New osCommerce Profile');
        parent::__construct();
    }
}
