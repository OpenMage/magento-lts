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

namespace Mage\Adminhtml\Test\Block\Sales\Order\Create;

use Mage\SalesRule\Test\Fixture\SalesRule;
use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Template;

/**
 * Adminhtml sales order coupons block.
 */
class Coupons extends Form
{
    /**
     * Backend abstract block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Click apply button selector.
     *
     * @var string
     */
    protected $applyButton = '//*[@id="coupons:code"]/following-sibling::button';

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

    /**
     * Apply Sales rule coupon.
     *
     * @param SalesRule $salesRule
     * @return void
     */
    public function applyCouponsCode(SalesRule $salesRule)
    {
        parent::fill($salesRule);
        $this->_rootElement->find($this->applyButton, Locator::SELECTOR_XPATH)->click();
        $this->getTemplateBlock()->waitLoader();
    }
}
