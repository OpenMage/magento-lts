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
 * @package     Mage_Usa
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fedex packaging source implementation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Fedex_Source_Unitofmeasure
{
    /**
     * Return array of Measure units
     *
     * @return array
     */
    public function toOptionArray()
    {
        $measureUnits = Mage::getSingleton('usa/shipping_carrier_fedex')->getCode('unit_of_measure');
        $result = [];
        foreach ($measureUnits as $key => $val){
            $result[] = ['value'=>$key,'label'=>$val];
        }
        return $result;
    }
}
