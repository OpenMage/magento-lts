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
 * @category   Mage
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source model for DHL Content Type
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_International_Source_Contenttype
{
    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => Mage::helper('usa')->__('Documents'),
                'value' => Mage_Usa_Model_Shipping_Carrier_Dhl_International::DHL_CONTENT_TYPE_DOC],
            ['label' => Mage::helper('usa')->__('Non documents'),
                'value' => Mage_Usa_Model_Shipping_Carrier_Dhl_International::DHL_CONTENT_TYPE_NON_DOC],
        ];
    }
}
