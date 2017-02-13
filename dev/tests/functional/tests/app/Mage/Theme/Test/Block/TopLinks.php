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

namespace Mage\Theme\Test\Block;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Page Top Links block.
 */
class TopLinks extends Block
{
    /**
     * Account link locator.
     *
     * @var string
     */
    protected $accountLink = '[data-target-element="#header-account"]';

    /**
     * Account label locator.
     *
     * @var string
     */
    protected $accountLabel = '[data-target-element="#header-account"]';

    /**
     * Account links block css selector.
     *
     * @var string
     */
    protected $accountLinksBlock = './ancestor::body//*[@id="header-account"]';

    /**
     * Mini cart link selector.
     *
     * @var string
     */
    protected $cartLink = '[data-target-element="#header-cart"]';

    /**
     * Mini cart content selector.
     *
     * @var string
     */
    protected $cartContent = '.minicart-wrapper';

    /**
     * Open mini cart.
     *
     * @return void
     */
    public function openMiniCart()
    {
        if (!$this->_rootElement->find($this->cartContent)->isVisible()) {
            $this->_rootElement->find($this->cartLink)->click();
        }
    }

    /**
     * Open Account Link.
     *
     * @return void
     */
    public function openAccount()
    {
        $this->_rootElement->find($this->accountLink)->click();
    }

    /**
     * Get account label text.
     *
     * @return string
     */
    public function getAccountLabelText()
    {
        return $this->_rootElement->find($this->accountLabel)->getText();
    }

    /**
     * Open account's links.
     *
     * @param string $linkTitle
     * @return void
     */
    public function openAccountLink($linkTitle)
    {
        $this->openAccount();
        $this->getAccountLinksBlock()->openLink($linkTitle);
    }

    /**
     * Get accounts link block.
     *
     * @return Links
     */
    protected function getAccountLinksBlock()
    {
        return $this->blockFactory->create(
            'Mage\Theme\Test\Block\Links',
            ['element' => $this->_rootElement->find($this->accountLinksBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
