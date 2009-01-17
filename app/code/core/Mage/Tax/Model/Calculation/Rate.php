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
 * Tax Rate Model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Calculation_Rate extends Mage_Core_Model_Abstract
{
    protected $_titles = null;
    protected $_titleModel = null;
    protected function _construct()
    {
        $this->_init('tax/calculation_rate');
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        $country = $this->getTaxCountryId();
        $region = $this->getTaxRegionId();
        $regionModel = Mage::getModel('directory/region');
        $regionModel->load($region);
        if ($regionModel->getCountryId() != $country) {
            $this->setTaxRegionId('*');
        }
    }

    protected function _afterSave()
    {
        $this->saveTitles();
        parent::_afterSave();
    }

    public function saveTitles($titles = null)
    {
        if (is_null($titles)) {
            $titles = $this->getTitle();
        }

        $this->getTitleModel()->deleteByRateId($this->getId());
        if (is_array($titles) && $titles) {
            foreach ($titles as $store=>$title) {
                if ($title !== '') {
                    $this->getTitleModel()
                        ->setId(null)
                        ->setTaxCalculationRateId($this->getId())
                        ->setStoreId((int) $store)
                        ->setValue($title)
                        ->save();
                }
            }
        }
    }

    public function getTitleModel()
    {
        if (is_null($this->_titleModel)) {
            $this->_titleModel = Mage::getModel('tax/calculation_rate_title');
        }
        return $this->_titleModel;
    }

    public function getTitles()
    {
        if (is_null($this->_titles)) {
            $this->_titles = $this->getTitleModel()->getCollection()->loadByRateId($this->getId());
        }
        return $this->_titles;
    }

    public function deleteAllRates()
    {
    	$this->_getResource()->deleteAllRates();
    	return $this;
    }
}