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
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Options for Code Format Field in Auto Generated Specific Coupon Codes configuration section
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_System_Config_Source_Coupon_Format
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $formatsList = Mage::helper('salesrule/coupon')->getFormatsList();
        $result = [];
        foreach ($formatsList as $formatId => $formatTitle) {
            $result[] = [
                'value' => $formatId,
                'label' => $formatTitle
            ];
        }

        return $result;
    }
}
