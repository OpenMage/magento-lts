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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\Attribute\Option;

/**
 * Attribute form.
 */
class Attribute extends Form
{
    /**
     * Item option xpath selector.
     *
     * @var string
     */
    protected $itemOption = './/li[@class="attribute-value" and div/*[text() = "%s"]]';

    /**
     * Fill item attribute.
     *
     * @param array $attribute
     * @return void
     */
    public function fillAttribute(array $attribute)
    {
        $options = $attribute['options'];
        unset($attribute['options']);
        $mapping = $this->dataMapping($attribute);
        $this->_fill($mapping);
        $this->fillOptions($options);
    }

    /**
     * Get item attribute.
     *
     * @param array $attribute
     * @return array
     */
    public function getAttribute(array $attribute)
    {
        $options = $attribute['options'];
        unset($attribute['options']);
        $mapping = $this->dataMapping($attribute);

        return array_merge($this->_getData($mapping), ['options' => $this->getOptions($options)]);
    }

    /**
     * Fill attribute options.
     *
     * @param array $options
     * @return void
     */
    protected function fillOptions(array $options)
    {
        $optionFields = [
            'price',
            'price_type',
            'admin'
        ];
        foreach ($options as $option) {
            $option = array_intersect_key($option, array_flip($optionFields));
            $this->getItemOption($option['admin'])->fillOption($option);
        }
    }

    /**
     * Get attribute options.
     *
     * @param array $options
     * @return array
     */
    protected function getOptions(array $options)
    {
        $result = [];
        $optionFields = [
            'price',
            'price_type',
            'admin'
        ];
        foreach ($options as $key => $option) {
            $option = array_intersect_key($option, array_flip($optionFields));
            $result[$key] = array_merge(
                $this->getItemOption($option['admin'])->getOption($option),
                ['admin' => $option['admin']]
            );
        }

        return $result;
    }

    /**
     * Get item option form.
     *
     * @param string $key
     * @return Option
     */
    protected function getItemOption($key)
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\Attribute\Option',
            ['element' => $this->_rootElement->find(sprintf($this->itemOption, $key), Locator::SELECTOR_XPATH)]
        );
    }
}
