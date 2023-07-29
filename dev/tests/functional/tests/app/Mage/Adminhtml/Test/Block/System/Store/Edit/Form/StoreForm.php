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

namespace Mage\Adminhtml\Test\Block\System\Store\Edit\Form;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;

/**
 * Form for Store View creation.
 */
class StoreForm extends Form
{
    /**
     * Store name selector in dropdown.
     *
     * @var string
     */
    protected $store = '//option[contains(.,"%s")]';

    /**
     * Check that Store visible in Store dropdown.
     *
     * @param string $name
     * @return bool
     */
    public function isStoreVisible($name)
    {
        return $this->_rootElement->find(sprintf($this->store, $name), Locator::SELECTOR_XPATH)->isVisible();
    }
}
