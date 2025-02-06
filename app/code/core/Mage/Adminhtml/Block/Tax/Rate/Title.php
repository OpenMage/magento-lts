<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Tax Rate Titles Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Rate_Title extends Mage_Core_Block_Template
{
    protected $_titles;

    protected function _construct()
    {
        $this->setTemplate('tax/rate/title.phtml');
    }

    public function getTitles()
    {
        if (is_null($this->_titles)) {
            $this->_titles = [];
            $titles = Mage::getSingleton('tax/calculation_rate')->getTitles();
            foreach ($titles as $title) {
                $this->_titles[$title->getStoreId()] = $title->getValue();
            }
            foreach ($this->getStores() as $store) {
                if (!isset($this->_titles[$store->getId()])) {
                    $this->_titles[$store->getId()] = '';
                }
            }
        }
        return $this->_titles;
    }

    public function getStores()
    {
        $stores = $this->getData('stores');
        if (is_null($stores)) {
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(false)
                ->load();
            $this->setData('stores', $stores);
        }
        return $stores;
    }
}
