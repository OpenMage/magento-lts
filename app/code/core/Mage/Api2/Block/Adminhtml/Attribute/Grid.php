<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 attributes grid block
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Block_Adminhtml_Attribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid ID
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setId('api2_attributes');
    }

    /**
     * Collection object set up
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();

        foreach (Mage_Api2_Model_Auth_User::getUserTypes() as $type => $label) {
            $collection->addItem(
                new Varien_Object(['user_type_name' => $label, 'user_type_code' => $type]),
            );
        }

        $this->setCollection($collection);

        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('user_type_name', [
            'header'    => $this->__('User Type'),
            'index'     => 'user_type_name',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Disable unnecessary functionality
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return $this;
    }

    /**
     * Get row URL
     *
     * @param  Varien_Object $row
     * @return null|string
     */
    public function getRowUrl($row)
    {
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        if ($session->isAllowed('system/api/attributes/edit')) {
            return $this->getUrl('*/*/edit', ['type' => $row->getUserTypeCode()]);
        }

        return null;
    }
}
