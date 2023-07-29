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

namespace Mage\PayPal\Test\Block\Express;

use Magento\Mtf\Block\Block;

/**
 * Pay Pal express checkout block on checkout cart page.
 */
class Shortcut extends Block
{
    /**
     * Click on 'Checkout with Pay Pal' button.
     *
     * @return void
     */
    public function checkoutWithPayPal()
    {
        $this->_rootElement->click();
    }
}
