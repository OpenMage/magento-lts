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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Paypal\Test\Block;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Client\Locator;

/**
 * Login to Pay Pal account.
 */
class Login extends Form
{
    /**
     * 'Log in to Pay Pal' button selector.
     *
     * @var string
     */
    protected $submitButton = '#btnLogin';

    /**
     * Loader selector.
     *
     * @var string
     */
    protected $loader = '#spinner';

    /**
     * I-frame selector.
     *
     * @var string
     */
    protected $iFrame = 'iframe';

    /**
     * I-frame selector.
     *
     * @var string
     */
    protected $frameBody = 'body';

    /**
     * Click 'Log in to Pay Pal' button.
     *
     * @return void
     */
    public function submit()
    {
        $rootElement = $this->findRootElement();
        $rootElement->find($this->submitButton)->click();
    }

    /**
     * Find root element for "Log In" button.
     *
     * @return \Magento\Mtf\Client\ElementInterface
     */
    public function findRootElement()
    {
        return $rootElement = ($this->browser->find($this->frameBody)->isVisible())
            ? $this->browser->find($this->frameBody)
            : $this->_rootElement;
    }

    /**
     * Select window of PayPal Express checkout iFrame, if need.
     *
     * @param null $element
     * @return \Magento\Mtf\Client\ElementInterface|null
     */
    public function switchOnPayPalFrame($element = null)
    {
        if ($this->browser->find($this->iFrame)->isVisible()) {
            $this->browser->switchToFrame(new Locator($this->iFrame));
            $element = $this->browser->find($this->frameBody);
    }

        return $element;
    }

    /**
     * Select window of PayPal Express checkout, if iFrame had been selected
     */
    public function switchOffPayPalFrame()
    {
            $this->browser->switchToFrame();
    }

    /**
     * Fill the root form.
     *
     * @param FixtureInterface $customer
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $customer, Element $element = null)
    {
        $element = $this->switchOnPayPalFrame($element);
        $this->waitForElementNotVisible($this->loader);
        return parent::fill($customer, $element);
    }
}
