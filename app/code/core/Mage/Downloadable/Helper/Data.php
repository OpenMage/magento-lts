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
 * @package     Mage_Downloadable
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable helper
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check is link shareable or not
     *
     * @param Mage_Downloadable_Model_Link | Mage_Downloadable_Model_Link_Purchased_Item $link
     * @return bool
     */
    public function getIsShareable($link)
    {
        $shareable = false;
        switch ($link->getIsShareable()) {
            case Mage_Downloadable_Model_Link::LINK_SHAREABLE_YES:
            case Mage_Downloadable_Model_Link::LINK_SHAREABLE_NO:
                $shareable = (bool) $link->getIsShareable();
                break;
            case Mage_Downloadable_Model_Link::LINK_SHAREABLE_CONFIG:
                $shareable = (bool) Mage::getStoreConfigFlag(
                    Mage_Downloadable_Model_Link::XML_PATH_CONFIG_IS_SHAREABLE
                );
        }
        return $shareable;
    }

    /**
     * Return true if price in website scope
     *
     * @return bool
     */
    public function getIsPriceWebsiteScope()
    {
        $scope =  (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);
        if ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
            return true;
        }
        return false;
    }
}
