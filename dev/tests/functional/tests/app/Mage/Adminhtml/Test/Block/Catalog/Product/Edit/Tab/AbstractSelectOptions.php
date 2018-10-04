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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab;

use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;

/**
 * Parent class for all selected forms of product options.
 */
abstract class AbstractSelectOptions extends AbstractOptions
{
    /**
     * Add button css selector.
     *
     * @var string
     */
    protected $addButton = ".scalable.add";

    /**
     * Item option css selector.
     *
     * @var string
     */
    protected $itemOption = './/tbody/tr[%d]';

    /**
     * Item options css selector.
     *
     * @var string
     */
    protected $itemOptions = './/tbody/tr';

    /**
     * Fills in the form of an array of input data.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillOptions(array $fields, Element $element = null)
    {
        $context = $element ? $element : $this->_rootElement;
        foreach ($fields as $key => $value) {
            $this->clickAddNewOptionsButton();
            $element = $context->find(sprintf($this->itemOption, ++$key), Locator::SELECTOR_XPATH);
            parent::fillOptions($value, $element);
        }

        return $this;
    }

    /**
     * Click on add new options button.
     *
     * @return void
     */
    protected function clickAddNewOptionsButton()
    {
        $this->_rootElement->find($this->addButton)->click();
    }

    /**
     * Getting options data form on the product form.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getOptions(array $fields = null, Element $element = null)
    {
        $result = [];
        $context = $element ? $element : $this->_rootElement;
        $items = $context->getElements($this->itemOptions, Locator::SELECTOR_XPATH);
        foreach ($items as $key => $value) {
            $result[$key] = parent::getOptions($fields[$key], $value);
        }

        return $result;
    }
}
