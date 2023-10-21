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

namespace Mage\Adminhtml\Test\Block\Review\Edit;

use Mage\Adminhtml\Test\Block\Widget\Tab;
use Magento\Mtf\Client\Element\SimpleElement as Element;

/**
 * Review details tab.
 */
class ReviewDetails extends Tab
{
    /**
     * Selector for customer.
     *
     * @var string
     */
    protected $customer = '#customer';

    /**
     * Get data of tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        return array_merge(parent::getDataFormTab($fields, $element), ['customer' => $this->getCustomer()]);
    }

    /**
     * Get customer.
     *
     * @return string
     */
    protected function getCustomer()
    {
        return $this->_rootElement->find($this->customer)->getText();
    }
}
