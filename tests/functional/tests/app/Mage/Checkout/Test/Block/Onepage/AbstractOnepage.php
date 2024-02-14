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
