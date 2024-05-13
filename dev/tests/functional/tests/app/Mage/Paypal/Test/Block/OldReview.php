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

namespace Mage\Paypal\Test\Block;

/**
 * Pay Pal sandbox old review block.
 */
class OldReview extends AbstractReview
{
    /**
     * Continue button selector.
     *
     * @var string
     */
    protected $continue = '#continue_abovefold';

    /**
     * Change shipping button selector.
     *
     * @var string
     */
    protected $changeShipping = '#changeAddressButton';

    /**
     * Addresses block selector.
     *
     * @var string
     */
    protected $addresses = '#innerSlider';

    /**
     * Get old addresses block.
     *
     * @return OldAddresses
     */
    public function getAddressesBlock()
    {
        return $this->blockFactory->create(
            'Mage\Paypal\Test\Block\OldAddresses',
            ['element' => $this->_rootElement->find($this->addresses)]
        );
    }
}
