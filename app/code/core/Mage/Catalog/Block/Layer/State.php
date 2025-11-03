<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Layered navigation state
 *
 * @package    Mage_Catalog
 *
 * @method $this setLayer(Mage_Catalog_Model_Layer $value)
 */
class Mage_Catalog_Block_Layer_State extends Mage_Core_Block_Template
{
    /**
     * Initialize Layer State template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/layer/state.phtml');
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $filters = $this->getLayer()->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = [];
        }

        return $filters;
    }

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        $filterState = [];
        foreach ($this->getActiveFilters() as $item) {
            $filter = $item->getFilter();
            $filterState[$filter->getRequestVar()] = $filter->getCleanValue();
        }

        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $filterState;
        $params['_escape']      = true;
        return Mage::getUrl('*/*/*', $params);
    }

    /**
     * Retrieve Layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        if (!$this->hasData('layer')) {
            $this->setLayer(Mage::getSingleton('catalog/layer'));
        }

        return $this->_getData('layer');
    }
}
