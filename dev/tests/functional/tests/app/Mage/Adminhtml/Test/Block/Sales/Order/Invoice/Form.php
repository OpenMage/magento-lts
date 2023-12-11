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

namespace Mage\Adminhtml\Test\Block\Sales\Order\Invoice;

use Mage\Adminhtml\Test\Block\Sales\Order\AbstractForm;

/**
 * Invoice create form.
 */
class Form extends AbstractForm
{
    /**
     * Items block css selector.
     *
     * @var string
     */
    protected $items = '#invoice_item_container';

    /**
     * Item block class.
     *
     * @var string
     */
    protected $itemBlockClass = 'Mage\Adminhtml\Test\Block\Sales\Order\Invoice\Form\Items';

    /**
     * Fill items data.
     *
     * @param array $data
     * @param array $products
     * @return void
     */
    protected function fillItemsData(array $data, array $products)
    {
        parent::fillItemsData($data, $products);
        $this->updateQty();
    }
}
