<?php
/**
 * Reports resource helper interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
