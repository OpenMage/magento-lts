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
 * @package     Mage_Api2
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 attributes grid block
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Block_Adminhtml_Attribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid ID
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setId('api2_attributes');
    }

    /**
     * Collection object set up
     */
    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();

        foreach (Mage_Api2_Model_Auth_User::getUserTypes() as $type => $label) {
            $collection->addItem(
                new Varien_Object(array('user_type_name' => $label, 'user_type_code' => $type))
            );
        }

        $this->setCollection($collection);
    }

    /**
     * Prepare grid columns
     *
     * @return Mage_Api2_Block_Adminhtml_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('user_type_name', array(
            'header'    => $this->__('User Type'),
            'index'     => 'user_type_name'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Disable unnecessary functionality
     *
     * @return Mage_Api2_Block_Adminhtml_Attribute_Grid
     */
    public function _prepareLayout()
    {
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return $this;
    }

    /**
     * Get row URL
     *
     * @param Varien_Object $row
     * @return string|null
     */
    public function getRowUrl($row)
    {
        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');
        if ($session->isAllowed('system/api/attributes/edit')) {
            return $this->getUrl('*/*/edit', array('type' => $row->getUserTypeCode()));
        }

        return null;
    }
}
