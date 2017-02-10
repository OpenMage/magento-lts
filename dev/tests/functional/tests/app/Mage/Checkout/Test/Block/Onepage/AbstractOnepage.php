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

namespace Mage\Checkout\Test\Block\Onepage;

use Magento\Mtf\Block\Form;

/**
 * Abstract form for all checkout forms.
 */
abstract class AbstractOnepage extends Form
{
    /**
     * Checkout loader selector.
     *
     * @var string
     */
    protected $waiterSelector = '.please-wait';

    /**
     * Continue checkout button selector.
     *
     * @var string
     */
    protected $continue;

    /**
     * Click continue button.
     *
     * @throws \Exception
     * @return void
     */
    public function clickContinue()
    {
        if (isset($this->continue)) {
            $this->_rootElement->find($this->continue)->click();
            $this->waitLoader();
        } else {
            throw new \Exception('Selector for continue button must be set!');
        }
    }

    /**
     * Wait for checkout loader.
     *
     * @return void
     */
    protected function waitLoader()
    {
        $selector = $this->waiterSelector;
        $browser = $this->_rootElement;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $element = $browser->find($selector);
                return $element->isVisible() == false ? true : null;
            }
        );
    }
}
