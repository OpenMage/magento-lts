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

use Mage\Customer\Test\Fixture\Address;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Additional Address block on customer address page.
 */
class AdditionalAddress extends Block
{
    /**
     * Address item selector.
     *
     * @var string
     */
    protected $addressItem = '//li[address[contains(.,"%s")]]';

    /**
     * Block text selector.
     *
     * @var string
     */
    protected $blockText = 'ol';

    /**
     * Delete address button selector.
     *
     * @var string
     */
    protected $deleteAddress = '.link-remove';

    /**
     * Delete additional address.
     *
     * @param Address $address
     * @return void
     */
    public function deleteAddress(Address $address)
    {
        $addressItemSelector = sprintf($this->addressItem, $address->getStreet());
        $addressItem = $this->_rootElement->find($addressItemSelector, Locator::SELECTOR_XPATH);
        $addressItem->find($this->deleteAddress)->click();
        $this->browser->acceptAlert();
    }

    /**
     * Get block text.
     *
     * @return string
     */
    public function getBlockText()
    {
        return $this->_rootElement->find($this->blockText)->getText();
    }
}
