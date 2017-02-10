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

namespace Mage\Adminhtml\Test\Block\System\Currency;

use Mage\Adminhtml\Test\Block\PageActions;
use Mage\Core\Test\Block\Messages;

/**
 * Grid page actions on the SystemCurrencyIndex page.
 */
class GridPageActions extends PageActions
{
    /**
     * Import button locator.
     *
     * @var string
     */
    protected $importButton = '.scalable.add';

    /**
     * Message block css selector.
     *
     * @var string
     */
    protected $message = '#messages';

    /**
     * "Save Currency Rates" button locator.
     *
     * @var string
     */
    protected $saveCurrentRate = '.scalable.save';

    /**
     * Click Import button.
     *
     * @throws \Exception
     * @return void
     */
    public function clickImportButton()
    {
        $this->_rootElement->find($this->importButton)->click();

        //Wait message
        $browser = $this->browser;
        $selector = $this->message;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $message = $browser->find($selector);
                return $message->isVisible() ? true : null;
            }
        );
        if ($this->getMessageBlock()->isVisibleMessage('warning')) {
            throw new \Exception($this->getMessageBlock()->getWarningMessages());
        }
    }

    /**
     * Get message block.
     *
     * @return Messages
     */
    protected function getMessageBlock()
    {
        return $this->blockFactory->create(
            'Mage\Core\Test\Block\Messages',
            ['element' => $this->_rootElement->find($this->message)]
        );
    }

    /**
     * Save Currency Rates.
     *
     * @return void
     */
    public function saveCurrentRate()
    {
        $this->_rootElement->find($this->saveCurrentRate)->click();
    }
}
