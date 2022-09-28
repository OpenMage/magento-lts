<?php
/**
 * OpenMage
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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
