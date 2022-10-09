<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Page\Test\Block\Html;

use Magento\Mtf\Block\Block;
use Mage\Adminhtml\Test\Fixture\Store;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\Element\SimpleElement;

/**
 * Header block.
 */
class Header extends Block
{
    /**
     * Dropdown store switcher selector.
     *
     * @var string
     */
    protected $storeSwitcher = '#select-language';

    /**
     * Language form.
     *
     * @var string
     */
    protected $languageForm = '.form-language';

    /**
     * Check is Store View Visible.
     *
     * @param Store $store
     * @return bool
     */
    public function isStoreViewVisible(Store $store)
    {
        $availableStores = explode("\n", $this->_rootElement->find($this->storeSwitcher)->getText());

        return in_array(strtoupper($store->getName()), $availableStores);
    }

    /**
     * Check if StoreView dropdown is visible.
     *
     * @return bool
     */
    public function isStoreViewDropdownVisible()
    {
        return $this->_rootElement->find($this->storeSwitcher)->isVisible();
    }

    /**
     * Select store.
     *
     * @param string $store
     * @param SimpleElement|null $element
     * @return void
     */
    public function selectStore($store, SimpleElement $element = null)
    {
        $element = ($element === null) ? $this->_rootElement : $element;
        $storeSwitcher = $element->find($this->storeSwitcher, Locator::SELECTOR_CSS, 'select');
        if ($storeSwitcher->isVisible()) {
            $storeSwitcher->setValue($store);
        }
    }
}
