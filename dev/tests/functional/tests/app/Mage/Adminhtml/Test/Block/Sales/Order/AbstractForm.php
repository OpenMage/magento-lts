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

namespace Mage\Adminhtml\Test\Block\Sales\Order;

use Magento\Mtf\Block\Form;
use Mage\Adminhtml\Test\Block\Template;
use Magento\Mtf\Client\Locator;

/**
 * Abstract Form block.
 */
abstract class AbstractForm extends Form
{
    /**
     * Backend abstract block selector.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Send button css selector.
     *
     * @var string
     */
    protected $send = '.submit-button';

    /**
     * Disabled flag.
     *
     * @var string
     */
    protected $disabledFlag = '.disabled';

    /**
     * Item block class.
     *
     * @var string
     */
    protected $itemBlockClass;

    /**
     * Items block css selector.
     *
     * @var string
     */
    protected $items;

    /**
     * Fill form data.
     *
     * @param array $data
     * @param array|null $products
     * @return void
     */
    public function fillData(array $data, $products = null)
    {
        if (isset($data['form_data'])) {
            $data['form_data'] = $this->dataMapping($data['form_data']);
            $this->_fill($data['form_data']);
        }
        if (isset($data['items_data']) && $products !== null) {
            $this->fillItemsData($data['items_data'], $products);
        }
    }

    /**
     * Fill items data.
     *
     * @param array $data
     * @param array $products
     * @return void
     */
    protected function fillItemsData(array $data, array $products)
    {
        foreach ($products as $key => $product) {
            $this->getItemsBlock()->getItemProductBlock($product)->fillProduct($data[$key]);
        }
    }

    /**
     * Click update qty's button.
     *
     * @return void
     */
    public function updateQty()
    {
        $this->getItemsBlock()->clickUpdateQty();
    }

    /**
     * Get items block.
     *
     * @return AbstractItemsNewBlock
     */
    public function getItemsBlock()
    {
        return $this->blockFactory->create(
            $this->itemBlockClass,
            ['element' => $this->_rootElement->find($this->items)]
        );
    }

    /**
     * Submit order.
     *
     * @return void
     */
    public function submit()
    {
        $this->getTemplateBlock()->waitLoader();
        $browser = $this->browser;
        $selector = $this->send . $this->disabledFlag;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $element = $browser->find($selector);
                return $element->isVisible() == false ? true : null;
            }
        );
        $this->_rootElement->find($this->send)->click();
    }

    /**
     * Get backend abstract block.
     *
     * @return Template
     */
    protected function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
