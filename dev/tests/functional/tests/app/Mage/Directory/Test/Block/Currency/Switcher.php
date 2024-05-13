<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Directory\Test\Block\Currency;

use Mage\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Switcher Currency Symbol.
 */
class Switcher extends Block
{
    /**
     * Currency switch locator.
     *
     * @var string
     */
    protected $currencySwitch = '#select-currency';

    /**
     * Selected Currency switch locator.
     *
     * @var string
     */
    protected $currencySwitchSelected = '#select-currency [selected="selected"]';

    /**
     * Switch currency to specified one.
     *
     * @param CurrencySymbolEntity $currencySymbol
     * @return void
     */
    public function switchCurrency(CurrencySymbolEntity $currencySymbol)
    {
        $this->waitForElementVisible($this->currencySwitch);
        $customCurrencySwitch = explode(" - ", $this->_rootElement->find($this->currencySwitchSelected)->getText());
        $currencyCode = $currencySymbol->getCode();
        if ($customCurrencySwitch[1] !== $currencyCode) {
            $this->_rootElement->find($this->currencySwitch, Locator::SELECTOR_CSS, 'select')
                ->setValue($currencyCode);
        }
    }
}
