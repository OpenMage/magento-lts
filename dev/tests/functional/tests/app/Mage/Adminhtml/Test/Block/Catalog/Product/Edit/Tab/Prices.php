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

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab;

use Magento\Mtf\Client\Element\SimpleElement as Element;
use Mage\Adminhtml\Test\Block\Widget\Tab;

/**
 * Product prices tab.
 */
class Prices extends Tab
{
    /**
     * Price type css selector.
     *
     * @var string
     */
    protected $priceType = 'table#%s_table';

    /**
     * Class name 'Subform' of the main tab form.
     *
     * @var array
     */
    protected $childrenForm = [
        'group_price' => [
            'selector' => 'group_prices'
        ],
        'tier_price' => [
            'selector' => 'tiers'
        ]
    ];

    /**
     * Fill data to fields on tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $context = $element ? $element : $this->_rootElement;
        foreach ($this->childrenForm as $key => $value) {
            if (isset($fields[$key])) {
                $this->fillOptionsPrices([$key => $fields[$key]], $context);
                unset($fields[$key]);
            }
        }

        $data = $this->dataMapping($fields);
        $this->_fill($data, $element);

        return $this;
    }

    /**
     * Fill price options.
     *
     * @param array $options
     * @param Element $context
     * @return void
     */
    protected function fillOptionsPrices(array $options, Element $context)
    {
        $priceType = key($options);
        $this->getOptionBlock($priceType, $context)->fillOptions($options[$priceType]['value']);
    }

    /**
     * Get data of tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $result = [];
        $context = $element ? $element : $this->_rootElement;
        foreach ($this->childrenForm as $key => $value) {
            if (isset($fields[$key])) {
                $result[$key] = $this->getOptionsPrices([$key => $fields[$key]], $context);
                unset($fields[$key]);
            }
        }
        $data = $this->dataMapping($fields);
        $result += $this->_getData($data, $element);

        return $result;
    }

    /**
     * Get price options.
     *
     * @param array $options
     * @param Element $context
     * @return array
     */
    protected function getOptionsPrices(array $options, Element $context)
    {
        $priceType = key($options);
        return $this->getOptionBlock($priceType, $context)->getOptions($options[$priceType]['value']);
    }

    /**
     * Get price options block.
     *
     * @param string $priceType
     * @param Element $context
     * @return AbstractSelectOptions
     */
    protected function getOptionBlock($priceType,  Element $context)
    {
        return $this->blockFactory->create(
            __NAMESPACE__ . '\\Prices\\Option' . ucfirst(str_replace('_price', '', $priceType)),
            ['element' => $context->find(sprintf($this->priceType, $this->childrenForm[$priceType]['selector']))]
        );
    }
}
