<?php
/**
 * OpenMage
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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Sales;

use Mage\Adminhtml\Test\Block\Widget\Grid;

/**
 * Sales abstract grid.
 */
abstract class AbstractGrid extends Grid
{
    /**
     * Locator value for link on action column.
     *
     * @var string
     */
    protected $editLink = 'td:nth-child(2)';

    /**
     * Name for id column.
     *
     * @var array
     */
    protected $idColumnName;

    /**
     * Get invoice ids
     *
     * @return array
     */
    public function getIds()
    {
        $result = [];
        $dataIds = $this->getRowsData(['id' => $this->idColumnName]);
        foreach ($dataIds as $key => $dataId) {
            $result[$key] = $dataId['id'];
        }

        return $result;
    }
}
