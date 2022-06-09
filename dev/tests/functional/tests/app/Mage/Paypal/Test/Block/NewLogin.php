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
namespace Mage\Paypal\Test\Block;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Client\Element\SimpleElement as Element;

/**
 * Login to Pay Pal account using the old form.
 */
class NewLogin extends Login
{
    /**
     * 'Log in' button selector.
     *
     * @var string
     */
    protected $submitButton = '#btnLogin';

    /**
     * 'Next' button selector.
     *
     * @var string
     */
    protected $nextButton = '#btnNext';

    /**
     * Button selector for start login.
     *
     * @var string
     */
    protected $startLoginButton = '.btn.full.ng-binding';

    /**
     * Fill the root form.
     *
     * @param FixtureInterface $customer
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $customer, Element $element = null)
    {
        $fullMapping = $this->mapping;

        $this->waitForElementNotVisible($this->loader);
        $this->_rootElement = $this->browser->find('.main');

        $this->overrideMapping($this->getFormSplitMapping('email'));
        parent::fill($customer, $this->switchOnPayPalFrame($element));

        if (!$this->browser->find($fullMapping['password']['selector'])->isVisible()) {
            $this->clickToElement($this->nextButton);
        }
        $this->overrideMapping($this->getFormSplitMapping('password'));
        parent::fill($customer, $this->switchOnPayPalFrame($element));

        return $this;
    }

    public function clickToElement($selector)
    {
        $rootElement = $this->findRootElement();
        $rootElement->find($selector)->click();
    }

    /**
     * Check is block active
     *
     * @return bool
     */
    public function isBlockActive()
    {
        if (!$this->browser->find($this->mapping['password']['selector'])->isVisible()) {
            return true;
        }

        return false;
    }

    /**
     * Override mapping to the form
     *
     * @param array $mapping
     * @return void
     */
    protected function overrideMapping(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * Get mapping to the form by key
     *
     * @return array
     */
    protected function getFormSplitMapping($key)
    {
        $mapping = $this->getFormMapping();
        if (empty($mapping)) {
            return [];
        }

        return isset($mapping['fields'][$key]) ? [$key => $mapping['fields'][$key]] : [];
    }
}
