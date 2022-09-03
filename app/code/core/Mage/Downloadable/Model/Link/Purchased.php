<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable links purchased model
 *
 * @method Mage_Downloadable_Model_Resource_Link_Purchased _getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased getResource()
 * @method int getOrderId()
 * @method $this setOrderId(int $value)
 * @method string getOrderIncrementId()
 * @method $this setOrderIncrementId(string $value)
 * @method int getOrderItemId()
 * @method $this setOrderItemId(int $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getProductName()
 * @method $this setProductName(string $value)
 * @method string getProductSku()
 * @method $this setProductSku(string $value)
 * @method string getLinkSectionTitle()
 * @method $this setLinkSectionTitle(string $value)
 * @method $this setPurchasedItems(Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection $value)
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Link_Purchased extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('downloadable/link_purchased');
        parent::_construct();
    }

    /**
     * Check order id
     *
     * @inheritDoc
     */
    public function _beforeSave()
    {
        if ($this->getOrderId() == null) {
            throw new Exception(
                Mage::helper('downloadable')->__('Order id cannot be null')
            );
        }
        return parent::_beforeSave();
    }
}
