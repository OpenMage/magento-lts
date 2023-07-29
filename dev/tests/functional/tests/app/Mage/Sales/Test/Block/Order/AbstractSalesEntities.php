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

/**
 * Abstract sales entity block on 'My Account'.
 */
abstract class AbstractSalesEntities extends Block
{
    /**
     * Format for sales entity id.
     *
     * @var int
     */
    protected $idFormat = '%d00000000';

    /**
     * Store view id.
     *
     * @var int
     */
    protected $storeViewId = 1;

    /**
     * Item entity class.
     *
     * @var string
     */
    protected $itemEntityClass;

    /**
     * Get item block.
     *
     * @param string $id
     * @return Block
     */
    public function getBlock($id)
    {
        return $this->blockFactory->create(
            $this->itemEntityClass,
            ['element' => $this->_rootElement, 'config' => ['id' => $this->prepareSalesEntityId($id)]]
        );
    }

    /**
     * Set store view id.
     *
     * @param int $storeViewId
     * @return void
     */
    public function setStoreViewId($storeViewId)
    {
        $this->storeViewId = $storeViewId;
    }

    /**
     * Prepare sales entity id.
     *
     * @param string $entityId
     * @return int
     */
    protected function prepareSalesEntityId($entityId)
    {
        return $entityId - sprintf($this->idFormat, $this->storeViewId);
    }
}
