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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;
use Magento\Mtf\Client\Element\SimpleElement as Element;

/**
 * Bundle items tab on product form.
 */
class Bundle extends Tab
{
    /**
     * Selector for 'Add New Option' button.
     *
     * @var string
     */
    protected $addNewOption = '#add_new_option';

    /**
     * Open option section.
     *
     * @var string
     */
    protected $openOption = '[data-target="#bundle_option_%d-content"]';

    /**
     * Option content selector.
     *
     * @var string
     */
    protected $optionContent = '#bundle_option_%d-content';

    /**
     * Bundle options block selector.
     *
     * @var string
     */
    protected $bundleOptionsBlock = '#bundle_option_%s';

    /**
     * Get bundle options block.
     *
     * @param int $blockNumber
     * @return Option
     */
    protected function getBundleOptionBlock($blockNumber)
    {
        return $this->blockFactory->create(
            'Mage\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option',
            ['element' => $this->_rootElement->find(sprintf($this->bundleOptionsBlock, $blockNumber))]
        );
    }

    /**
     * Fill bundle options.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (isset($fields['bundle_selections'])) {
            foreach ($fields['bundle_selections']['value'] as $key => $bundleOption) {
                $this->createAndEditOption($key);
                $this->getBundleOptionBlock($key)->fillOption($bundleOption);
            }
            unset($fields['bundle_selections']);
        }
        parent::fillFormTab($fields, $element);
    }

    /**
     * Open exist option for edit or create new.
     *
     * @param int $optionKey
     * @return void
     */
    protected function createAndEditOption($optionKey)
    {
        $itemOption = $this->_rootElement->find(sprintf($this->openOption, $optionKey));
        $isContent = $this->_rootElement->find(sprintf($this->optionContent, $optionKey))->isVisible();
        if ($itemOption->isVisible() && !$isContent) {
            $itemOption->click();
        } elseif (!$itemOption->isVisible()) {
            $this->_rootElement->find($this->addNewOption)->click();
        }
    }

    /**
     * Get data from fields on bundle items tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $newFields = [];
        if (isset($fields['bundle_selections'])) {
            foreach ($fields['bundle_selections']['value'] as $key => $bundleOption) {
                $bundleOption = $this->prepareBundleOptions($bundleOption);
                $newFields['bundle_selections'][$key] = $this->getBundleOptionBlock($key)->getOptionData($bundleOption);
            }
            unset($fields['bundle_selections']);
        }

        return array_merge($newFields, parent::getDataFormTab($fields, $element));
    }

    /**
     * Prepare bundle options.
     *
     * @param array $bundleOption
     * @return array
     */
    protected function prepareBundleOptions(array $bundleOption)
    {
        foreach ($bundleOption['assigned_products'] as $productKey => $product) {
            $bundleOption['assigned_products'][$productKey]['getProductSku'] = $product['sku'];
            $bundleOption['assigned_products'][$productKey]['getProductName'] = $product['name'];
        }

        return $bundleOption;
    }
}
