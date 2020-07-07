<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Sales\Order\Shipment;

use Mage\Adminhtml\Test\Block\Sales\Order\AbstractForm;
use Mage\Adminhtml\Test\Block\Sales\Order\Shipment\Form\Tracking;

/**
 * Shipment create form.
 */
class Form extends AbstractForm
{
    /**
     * Items block css selector.
     *
     * @var string
     */
    protected $items = '#ship_items_container';

    /**
     * Item block class.
     *
     * @var string
     */
    protected $itemBlockClass = 'Mage\Adminhtml\Test\Block\Sales\Order\Shipment\Form\Items';

    /**
     * Tracking block css selector.
     *
     * @var string
     */
    protected $tracking = '#tracking_numbers_table';

    /**
     * Get tracking block.
     *
     * @return Tracking
     */
    protected function getTrackingBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Shipment\Form\Tracking',
            ['element' => $this->_rootElement->find($this->tracking)]
        );
    }

    /**
     * Fill form data.
     *
     * @param array $data
     * @param array|null $products
     * @return void
     */
    public function fillData(array $data, $products = null)
    {
        if (isset($data['form_data']['tracking'])) {
            $this->getTrackingBlock()->fill($data['form_data']['tracking']);
            unset($data['form_data']['tracking']);
        }
        parent::fillData($data, $products);
    }
}
