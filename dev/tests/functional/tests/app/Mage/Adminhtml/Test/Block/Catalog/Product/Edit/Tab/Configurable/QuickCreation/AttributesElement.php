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

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable\QuickCreation;

use Magento\Mtf\Client\Element\SelectElement;
use Magento\Mtf\Client\Locator;

/**
 * Typified element class for attributes on creation form for configurable tab.
 */
class AttributesElement extends SelectElement
{
    /**
     * Selector for attribute input element.
     *
     * @var string
     */
    protected $attributeInput = '//tr[.//label[contains(text(),"%s")]]//select/option[@value=%s]';

    /**
     * Select attributes values.
     *
     * @param array $value
     * @return void
     */
    public function setValue($value)
    {
        $value = isset($value['value']) ? $value['value'] : $value;
        foreach ($value as $attributeCode => $itemValue) {
            $selector = sprintf($this->attributeInput, $attributeCode, $this->escapeQuotes($itemValue));
            $option = $this->find($selector, Locator::SELECTOR_XPATH);
            $option->click();
        }
    }

    /**
     * Skip this method.
     *
     * @return void
     */
    public function getValue()
    {
        //
    }
}
