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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Sales\Order\Create;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Template;

/**
 * Adminhtml sales order shipping block.
 */
class Shipping extends Block
{
    /**
     * 'Get shipping methods and rates' link.
     *
     * @var string
     */
    protected $shippingMethodsLink = '#order-shipping-method-summary a';

    /**
     * Shipping method selector.
     *
     * @var string
     */
    protected $shippingMethod = '//dt[contains(.,"%s")]/following-sibling::*//*[contains(text(), "%s")]';

    /**
     * Backend abstract block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Select shipping method.
     *
     * @param array $method
     * @return void
     */
    public function selectShippingMethod(array $method)
    {
        $this->_rootElement->find($this->shippingMethodsLink)->click();
        $selector = sprintf($this->shippingMethod, $method['shipping_service'], $method['shipping_method']);
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Get backend abstract block.
     *
     * @return Template
     */
    public function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
