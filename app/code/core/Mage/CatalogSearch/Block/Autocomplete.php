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
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Autocomplete queries list
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Block_Autocomplete extends Mage_Core_Block_Abstract
{
    /**
     * @var array
     */
    protected $_suggestData;

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _toHtml()
    {
        $html = '';

        if (!$this->_beforeToHtml()) {
            return $html;
        }

        $suggestData = $this->getSuggestData();
        if (!($count = count($suggestData))) {
            return $html;
        }

        $isAjaxSuggestionCountResultsEnabled = (bool) Mage::app()->getStore()
            ->getConfig(Mage_CatalogSearch_Model_Query::XML_PATH_AJAX_SUGGESTION_COUNT);

        $count--;

        $html = '<ul><li style="display:none"></li>';
        foreach ($suggestData as $index => $item) {
            if ($index == 0) {
                $item['row_class'] .= ' first';
            }

            if ($index == $count) {
                $item['row_class'] .= ' last';
            }

            $html .=  '<li title="' . $this->escapeHtml($item['title']) . '" class="' . $item['row_class'] . '">';
            if ($isAjaxSuggestionCountResultsEnabled) {
                $html .= '<span class="amount">' . $item['num_of_results'] . '</span>';
            }
            $html .= $this->escapeHtml($item['title']) . '</li>';
        }

        $html.= '</ul>';

        return $html;
    }

    /**
     * @return array
     */
    public function getSuggestData()
    {
        if (!$this->_suggestData) {
            /** @var Mage_CatalogSearch_Helper_Data $helper */
            $helper = $this->helper('catalogsearch');
            $collection = $helper->getSuggestCollection();
            $query = $helper->getQueryText();
            $counter = 0;
            $data = [];
            foreach ($collection as $item) {
                $_data = [
                    'title' => $item->getQueryText(),
                    'row_class' => (++$counter)%2?'odd':'even',
                    'num_of_results' => $item->getNumResults()
                ];

                if ($item->getQueryText() == $query) {
                    array_unshift($data, $_data);
                } else {
                    $data[] = $_data;
                }
            }
            $this->_suggestData = $data;
        }
        return $this->_suggestData;
    }
}
