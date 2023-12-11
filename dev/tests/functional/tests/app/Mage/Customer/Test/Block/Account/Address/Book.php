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

namespace Mage\Customer\Test\Block\Account\Address;

use Magento\Mtf\Block\Block;

/**
 * Address book block on customer address page.
 */
class Book extends Block
{
    /**
     * Additional address entries selector.
     *
     * @var string
     */
    protected $additionalAddress = '.addresses-additional';

    /**
     * Get additional address block.
     *
     * @return AdditionalAddress
     */
    public function getAdditionalAddressBlock()
    {
        return $this->blockFactory->create(
            'Mage\Customer\Test\Block\Account\Address\AdditionalAddress',
            ['element' => $this->_rootElement->find($this->additionalAddress)]
        );
    }
}
