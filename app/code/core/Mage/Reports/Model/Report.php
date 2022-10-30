<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Reports_Model_Report
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method int getPageSize()
 * @method $this setPageSize(int $value)
 * @method array getStoreIds()
 * @method $this setStoreIds( $value)
 * @method $this setDateRange(string $from, string $to)
 */
class Mage_Reports_Model_Report extends Mage_Core_Model_Abstract
{
    /**
     * @var Mage_Reports_Model_Report
     */
    protected $_reportModel;

    /**
     * @param string $modelClass
     * @return $this
     */
    public function initCollection($modelClass)
    {
        $this->_reportModel = Mage::getResourceModel($modelClass);

        return $this;
    }

    /**
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Report
     */
    public function getReportFull($from, $to)
    {
        return $this->_reportModel
            ->setDateRange($from, $to)
            ->setPageSize(false)
            ->setStoreIds($this->getStoreIds());
    }

    /**
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Report
     */
    public function getReport($from, $to)
    {
        return $this->_reportModel
            ->setDateRange($from, $to)
            ->setPageSize($this->getPageSize())
            ->setStoreIds($this->getStoreIds());
    }
}
