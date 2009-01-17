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

class Mage_Tax_Model_Rate extends Mage_Core_Model_Abstract
{
    protected $_dataCollection;

    protected function _construct()
    {
        $this->_init('tax/rate');
    }

    public function addRateData($rateData)
    {
        if (is_array($rateData) && isset($rateData['rate_type_id']) && isset($rateData['rate_value'])) {
            $rateDataModel = Mage::getModel('tax/rate_data')
                ->setRateTypeId($rateData['rate_type_id'])
                ->setRateValue($rateData['rate_value']);
        }
        elseif ($rateData instanceof Mage_Tax_Model_Rate_Data) {
            $rateDataModel = $rateData;
        }

        if (!$rateDataModel) {
            Mage::throwException(Mage::helper('tax')->__('Incorrect rate Data'));
        }

        $dataItem = $this->getRateDataCollection()->getItemByRateAndType(
            $this->getId(),
            $rateDataModel->getRateTypeId()
        );
        if ($dataItem) {
            $dataItem->addData($rateDataModel->getData());
        }
        else {
            $this->getRateDataCollection()->addItem($rateDataModel);
        }

        return $this;
    }

    public function getRateDataCollection()
    {
        if (is_null($this->_dataCollection)) {
            $this->_dataCollection = Mage::getModel('tax/rate_data')->getCollection();
            $this->_dataCollection->setRateFilter($this);
        }
        return $this->_dataCollection;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
//        if (!$this->getTaxPostcode()) {
//            $this->setTaxPostcode('*');
//        }
    }

    protected function _beforeSave()
    {
        if (!$this->hasTaxRegionId()) {
            $this->setTaxRegionId(0);
        }
        if (!$this->getTaxPostcode()) {
            $this->setTaxPostcode('*');
        }
        parent::_beforeSave();
    }

    protected function _afterSave()
    {
        foreach ($this->getRateDataCollection() as $dataModel) {
            $dataModel->setTaxRateId($this->getId());
            $dataModel->save();
        }
        parent::_afterSave();
    }

//    public function loadWithAttributes($rateId)
//    {
//        return $this->_getResource()->loadWithAttributes($rateId);
//    }

//    public function loadCollectionWithAttributes()
//    {
//        return $this->getCollection()
//            ->joinTypeData()
//            ->joinRegionTable();
//    }
//
//    //public function load
//
//    public function loadWithAttributes($rateId = 0)
//    {
//        return $this->_getResource()->loadWithAttributes($rateId);
//    }

    public function deleteAllRates()
    {
    	$this->_getResource()->deleteAllRates();
    	return $this;
    }
}