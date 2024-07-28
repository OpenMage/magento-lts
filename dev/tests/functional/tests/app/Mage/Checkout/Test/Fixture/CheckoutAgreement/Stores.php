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

namespace Mage\Checkout\Test\Fixture\CheckoutAgreement;

use Mage\Adminhtml\Test\Fixture\Store;
use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Fixture\FixtureFactory;

/**
 * Prepare Stores preset.
 */
class Stores extends DataSource
{
    /**
     * Array of stores fixtures.
     *
     * @var Store[]
     */
    public $stores;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $data, array $params = [])
    {
        $this->params = $params;
        if (isset($data['datasets'])) {
            foreach ($data['datasets'] as $store) {
                $store = $fixtureFactory->createByCode('store', ['dataset' => $store]);
                /** @var Store $store */
                if (!$store->getStoreId()) {
                    $store->persist();
                }
                $this->stores[] = $store;
                $this->data[] = $store->getGroupId() . '/' . $store->getName();
            }
        }
    }

    /**
     * Return array of stores fixtures.
     *
     * @return Store[]
     */
    public function getStores()
    {
        return $this->stores;
    }
}
