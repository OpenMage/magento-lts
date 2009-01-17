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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once "Varien/Pear.php";
require_once "Varien/Pear/Package.php";

abstract class Mage_Adminhtml_Model_Extension_Collection_Abstract extends Varien_Data_Collection
{

    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        // fetch packages specific to source
        $packages = $this->_fetchPackages();

        // apply filters
        if (!empty($this->_filters)) {
            foreach ($packages as $i=>$pkg) {
                if (!$this->validateRow($pkg)) {
                    unset($packages[$i]);
                }
            }
        }

        // find totals
        $this->_totalRecords = sizeof($packages);
        $this->_setIsLoaded();

        // sort packages
        if (!empty($this->_orders)) {
            usort($packages, array($this, 'sortPackages'));
        }

        // pagination and add to collection
        $from = ($this->getCurPage() - 1) * $this->getPageSize();
        $to = $from + $this->getPageSize() - 1;

        $cnt = 0;
        foreach ($packages as $pkg) {
            $cnt++;
            if ($cnt<$from || $cnt>$to) {
                continue;
            }
            $item = new $this->_itemObjectClass();
            $item->addData($pkg);
            $this->addItem($item);
        }

        return $this;
    }

    abstract protected function _fetchPackages();

    public function setOrder($field, $dir)
    {
        $this->_orders[] = array('field'=>$field, 'dir'=>$dir);
        return $this;
    }

    public function sortPackages($a, $b)
    {
        $field = $this->_orders[0]['field'];
        $dir = $this->_orders[0]['dir'];

        $cmp = $a[$field] > $b[$field] ? 1 : ($a[$field] < $b[$field] ? -1 : 0);

        return ('asc'===$dir) ? $cmp : -$cmp;
    }

    public function addFieldToFilter($field, $condition)
    {
        $this->_filters[$field] = $condition;
        return $this;
    }

    public function validateRow($row)
    {
        if (empty($this->_filters)) {
            return true;
        }
        foreach ($this->_filters as $field=>$filter) {
            if (!isset($row[$field])) {
                return false;
            }
            if (isset($filter['eq'])) {
                if ($filter['eq']!=$row[$field]) {
                    return false;
                }
            }
            if (isset($filter['like'])) {
                $query = preg_replace('#(^%|%$)#', '', $filter['like']);
                if (strpos(strtolower($row[$field]), strtolower($query))===false) {
                    return false;
                }
            }
            if ('version'===$field) {
                if (isset($filter['from'])) {
                    if (!version_compare($filter['from'], $row[$field], '<=')) {
                        return false;
                    }
                }
                if (isset($filter['to'])) {
                    if (!version_compare($filter['to'], $row[$field], '>=')) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function getAllIds()
    {
        $this->load();

        $ids = array();
        foreach ($this->getIterator() as $item) {
            $ids[] = $item->getId();
        }
        return $ids;
    }

}
