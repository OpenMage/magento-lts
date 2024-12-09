<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Reports resource helper interface
 *
 * @category   Mage
 * @package    Mage_Reports
 */
interface Mage_Reports_Model_Resource_Helper_Interface
{
    /**
     * Merge Index data
     *
     * @param string $mainTable
     * @param array $data
     * @param mixed $matchFields
     * @return int
     */
    public function mergeVisitorProductIndex($mainTable, $data, $matchFields);

    /**
     * Update rating position
     *
     * @param string $type day|month|year
     * @param string $column
     * @param string $mainTable
     * @param string $aggregationTable
     * @return Mage_Core_Model_Resource_Helper_Abstract
     */
    public function updateReportRatingPos($type, $column, $mainTable, $aggregationTable);
}
