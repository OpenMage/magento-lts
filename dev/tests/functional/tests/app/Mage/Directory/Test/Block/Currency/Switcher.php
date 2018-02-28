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
