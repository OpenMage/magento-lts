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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Admin\Test\Fixture\Role;

use Magento\Mtf\Fixture\FixtureInterface;
use Mage\Admin\Test\Fixture\User;

/**
 * Roles resources data source.
 */
class RolesResources implements FixtureInterface
{
    /**
     * Prepared dataset data.
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings.
     *
     * @var array
     */
    protected $params;

    /**
     * @constructor
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
        } elseif (isset($data['value'])) {
            $this->data = $data['value'];
        }
    }

    /**
     * Persist attribute options.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set.
     *
     * @param string|null $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings.
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Preset for Attribute manage options.
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'sales' => [
                'sales',
                'sales/archive',
                'sales/archive/creditmemos',
                'sales/archive/shipments',
                'sales/archive/invoices',
                'sales/archive/orders',
                'sales/archive/orders/remove',
                'sales/archive/orders/add',
                'sales/order',
                'sales/order/actions',
                'sales/order/actions/hold',
                'sales/order/actions/creditmemo',
                'sales/order/actions/unhold',
                'sales/order/actions/ship',
                'sales/order/actions/emails',
                'sales/order/actions/comment',
                'sales/order/actions/invoice',
                'sales/order/actions/capture',
                'sales/order/actions/email',
                'sales/order/actions/view',
                'sales/order/actions/reorder',
                'sales/order/actions/edit',
                'sales/order/actions/review_payment',
                'sales/order/actions/cancel',
                'sales/order/actions/create',
                'sales/order/actions/create/reward_spend',
                'sales/invoice',
                'sales/shipment',
                'sales/creditmemo',
                'sales/checkoutagreement',
                'sales/transactions',
                'sales/transactions/fetch',
                'sales/recurring_profile',
                'sales/billing_agreement',
                'sales/billing_agreement/actions',
                'sales/billing_agreement/actions/view',
                'sales/billing_agreement/actions/manage',
                'sales/billing_agreement/actions/use',
                'sales/tax',
                'sales/tax/classes_customer',
                'sales/tax/classes_product',
                'sales/tax/import_export',
                'sales/tax/rates',
                'sales/tax/rules'
            ]
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
