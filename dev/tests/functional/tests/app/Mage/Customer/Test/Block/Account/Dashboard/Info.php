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

namespace Mage\Customer\Test\Block\Account\Dashboard;

use Magento\Mtf\Block\Block;

/**
 * Main block on customer account page.
 */
class Info extends Block
{
    /**
     * Css selector for Contact Information Change Password Link.
     *
     * @var string
     */
    protected $contactInfoChangePasswordLink = '.box-content a';

    /**
     * Click on Contact Information Edit Link.
     *
     * @return void
     */
    public function openChangePassword()
    {
        $this->_rootElement->find($this->contactInfoChangePasswordLink)->click();
    }
}
