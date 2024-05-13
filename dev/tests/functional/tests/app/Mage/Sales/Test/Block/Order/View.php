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

namespace Mage\Sales\Test\Block\Order;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * View block on order's view page.
 */
class View extends Block
{
    /**
     * Link xpath selector.
     *
     * @var string
     */
    protected $link = '//a[text()="%s"]';

    /**
     * Open link by name.
     *
     * @param string $name
     * @return void
     */
    public function openLinkByName($name)
    {
        $this->_rootElement->find(sprintf($this->link, $name), Locator::SELECTOR_XPATH)->click();
    }
}
