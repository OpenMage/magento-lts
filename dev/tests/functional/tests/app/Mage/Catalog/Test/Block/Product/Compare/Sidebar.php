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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Block\Product\Compare;

use Magento\Mtf\Client\Element;

/**
 * Compare product block on cms page.
 */
class Sidebar extends ListCompare
{
    /**
     * Selector for empty message.
     *
     * @var string
     */
    protected $isEmpty = 'div.empty';

    /**
     * Product name selector.
     *
     * @var string
     */
    protected $productName = 'li.product-item.odd.last strong.product-item-name a';

    /**
     * Selector for "Clear All" button.
     *
     * @var string
     */
    protected $clearAll = '#compare-clear-all';

    /**
     * Get compare products block content.
     *
     * @throws \Exception
     * @return array|string
     */
    public function getProducts()
    {
        try {
            $result = [];
            $rootElement = $this->_rootElement;
            $selector = $this->productName;
            $this->_rootElement->waitUntil(
                function () use ($rootElement, $selector) {
                    return $rootElement->find($selector)->isVisible() ? true : null;
                }
            );
            $elements = $this->_rootElement->getElements($this->productName);
            foreach ($elements as $element) {
                $result[] = $element->getText();
            }
            return $result;
        } catch (\Exception $e) {
            $isEmpty = $this->_rootElement->find($this->isEmpty);
            if ($isEmpty->isVisible()) {
                return $isEmpty->getText();
            } else {
                throw $e;
            }
        }
    }

    /**
     * Click "Clear All" on "My Account" page.
     *
     * @return void
     */
    public function clickClearAll()
    {
        $this->_rootElement->find($this->clearAll)->click();
        $this->_rootElement->acceptAlert();
    }
}
