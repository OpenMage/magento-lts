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
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Autocomplete queries list
 */
class Mage_CatalogSearch_Block_Autocomplete extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $html = '';

		if (!$this->_beforeToHtml()) {
			return $html;
		}

		$collection = $this->helper('catalogSearch')->getSuggestCollection();
		if (!$collection->getSize()) {
		    return $html;
		}

		$query = $this->helper('catalogSearch')->getQueryText();
		$counter=0;

		$html = '<ul><li style="display:none"></li>';
		$itemsHtml = '';
		$firstHtml = '';
		foreach ($collection as $item) {
		    if ($item->getQueryText() == $query) {
                $firstHtml.= '<li title="'.$this->htmlEscape($item->getQueryText()).'" class="'.((++$counter)%2?'odd':'even').'">';
                $firstHtml.= '<div style="float:right">'.$item->getNumResults().'</div>'.$this->htmlEscape($item->getQueryText()).'</li>';
		    }
		    else {
		        $itemsHtml.= '<li title="'.$this->htmlEscape($item->getQueryText()).'" class="'.((++$counter)%2?'odd':'even').'">';
                $itemsHtml.= '<div style="float:right">'.$item->getNumResults().'</div>'.$this->htmlEscape($item->getQueryText()).'</li>';
		    }
		}

		$html.= $firstHtml.$itemsHtml;
		$html.= '</ul>';

        return $html;
    }
}