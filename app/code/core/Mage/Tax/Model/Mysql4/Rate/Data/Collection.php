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
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rate data collection
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tax_Model_Mysql4_Rate_Data_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/rate_data');
    }

    public function setRateFilter($rate)
    {
        if ($rate instanceof Mage_Tax_Model_Rate) {
            $this->addFieldToFilter('tax_rate_id', $rate->getId());
        }
        else {
            $this->addFieldToFilter('tax_rate_id', $rate);
        }
        return $this;
    }

    public function getItemByRateAndType($rateId, $rateTypeId)
    {
        if (!$rateId || !$rateTypeId) {
            return false;
        }
        foreach ($this as $item) {
            if ($item->getTaxRateId() == $rateId && $item->getRateTypeId()==$rateTypeId) {
                return $item;
            }
        }
        return false;
    }
}