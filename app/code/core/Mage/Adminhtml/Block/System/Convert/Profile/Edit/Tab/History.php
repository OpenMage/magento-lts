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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_History extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('history_grid');
        $this->setDefaultSort('performed_at', 'desc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('dataflow/profile_history_collection')
            ->joinAdminUser()
            ->addFieldToFilter('profile_id', Mage::registry('current_convert_profile')->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('action_code', array(
            'header'    => Mage::helper('adminhtml')->__('Profile Action'),
            'index'     => 'action_code',
            'filter'    => 'adminhtml/system_convert_profile_edit_filter_action',
            'renderer'  => 'adminhtml/system_convert_profile_edit_renderer_action',
        ));

        $this->addColumn('performed_at', array(
            'header'    => Mage::helper('adminhtml')->__('Performed At'),
            'type'      => 'datetime',
            'index'     => 'performed_at',
            'width'     => '150px',
        ));

        $this->addColumn('firstname', array(
            'header'    => Mage::helper('adminhtml')->__('First Name'),
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('adminhtml')->__('Last Name'),
            'index'     => 'lastname',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/history', array('_current' => true));
    }
}
