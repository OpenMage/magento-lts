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
 * Adminhtml Catalog Inventory Manage Stock Config Backend Model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Catalog_Inventory_Managestock
    extends Mage_Core_Model_Config_Data
{
    /**
     * @var Mage_CatalogInventory_Model_Stock_Status
     */
    protected $_stockStatusModel;

    public function __construct($parameters = array())
    {
        if (!empty($parameters['stock_status_model'])) {
            $this->_stockStatusModel = $parameters['stock_status_model'];
        } else {
            $this->_stockStatusModel = Mage::getSingleton('cataloginventory/stock_status');
        }

        parent::__construct($parameters);
    }

    /**
     * After change Catalog Inventory Manage value process
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_Catalog_Inventory_Managestock
     */
    protected function _afterSave()
    {
        if ($this->getValue() != $this->getOldValue()) {
            $this->_stockStatusModel->rebuild();
        }

        return $this;
    }
}
