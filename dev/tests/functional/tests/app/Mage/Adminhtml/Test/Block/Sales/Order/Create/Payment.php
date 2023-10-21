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

use Magento\Mtf\Block\Block;
use Mage\Adminhtml\Test\Block\Template;
use Magento\Mtf\Client\Locator;

/**
 * Adminhtml sales order payment block.
 */
class Payment extends Block
{
    /**
     * Payment method.
     *
     * @var string
     */
    protected $paymentMethod = '#p_method_%s';

    /**
     * Backend abstract block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Select payment method.
     *
     * @param array $paymentCode
     * @throws \Exception
     */
    public function selectPaymentMethod(array $paymentCode)
    {
        $paymentInput = $this->_rootElement->find(sprintf($this->paymentMethod, $paymentCode['method']));
        if ($paymentInput->isPresent()) {
            if ($paymentInput->isVisible()) {
                $paymentInput->click();
            }
            $this->getTemplateBlock()->waitLoader();
        } else {
            throw new \Exception("{$paymentCode['method']} method is not visible.");
        }
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
