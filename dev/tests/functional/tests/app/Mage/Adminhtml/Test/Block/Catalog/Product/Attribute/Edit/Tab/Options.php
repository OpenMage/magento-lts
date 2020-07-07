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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Attribute\Edit\Tab;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Adminhtml\Test\Block\Catalog\Product\Attribute\Edit\Tab\Options\Option;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;

/**
 * Options tab.
 */
class Options extends Tab
{
    /**
     * 'Add Option' button.
     *
     * @var string
     */
    protected $addOption = '#add_new_option_button';

    /**
     * Options block.
     *
     * @var string
     */
    protected $optionsBlock = './/div[@id = "matage-options-panel"]//table[@class = "dynamic-grid"]';

    /**
     * Options row.
     *
     * @var string
     */
    protected $optionsRow = '//tr[%d]';

    /**
     * Options selector.
     *
     * @var string
     */
    protected $optionsSelector = '//td[1]/input';

    /**
     * Fill 'Options' tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        foreach ($fields['options']['value'] as $field) {
            $this->_rootElement->find($this->addOption)->click();
            $this->getOptionsBlock()->fillOption($field);
        }
        return $this;
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
        $options = [];
        foreach ($fields['options']['value'] as $key => $field) {
            $options[] = $this->getOptionsBlock($key)->getOption($field);
        }
        $result['options'] = array_reverse($options);

        return $result;
    }

    /**
     * Get options block.
     *
     * @param int|null $key
     * @return Option
     */
    protected function getOptionsBlock($key = null)
    {
        $row = '';
        if ($key !== null) {
            $row = sprintf($this->optionsRow, $key + 2);
        }
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Catalog\Product\Attribute\Edit\Tab\Options\Option',
            ['element' => $this->_rootElement->find($this->optionsBlock . $row, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get options ids.
     *
     * @return array
     */
    public function getOptionsIds()
    {
        $result = [];
        $selector = $this->optionsBlock . $this->optionsSelector;
        $options = $this->_rootElement->getElements($selector, Locator::SELECTOR_XPATH);
        foreach ($options as $option) {
            $elementName = $option->getAttribute('name');
            preg_match('`option\[value\]\[(\d+)\]\[0\]`', $elementName, $matches);
            $result[$option->getValue()] = $matches[1];
        }

        return $result;
    }
}
