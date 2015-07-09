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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
