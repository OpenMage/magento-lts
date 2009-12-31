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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * DataFlow Import resource model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Mysql4_Import extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('dataflow/import', 'import_id');
    }

    public function select($sessionId)
    {
        return $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('session_id=?', $sessionId)
            ->where('status=?', 0);
    }

    public function loadBySessionId($sessionId, $min = 0, $max = 100)
    {
        if (!is_numeric($min) || !is_numeric($max)) {
            return array();
        }
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('dataflow/import'), '*')
            ->where('import_id between '.(int)$min.' and '.(int)$max)
            ->where('status=?', '0')
            ->where('session_id=?', $sessionId);
        return $read->fetchAll($select);
    }

    public function loadTotalBySessionId($sessionId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('dataflow/import'),
        array('max'=>'max(import_id)','min'=>'min(import_id)', 'cnt'=>'count(*)'))
            ->where('status=?', '0')
            ->where('session_id=?', $sessionId);
        return $read->fetchRow($select);
    }

    public function loadById($importId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('dataflow/import'),'*')
            ->where('status=?', 0)
            ->where('import_id=?', $importId);
        return $read->fetchRow($select);
    }

}
