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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


abstract class Mage_Core_Model_Mysql4_Collection_Abstract extends Varien_Data_Collection_Db
{
    /**
     * Model name
     *
     * @var string
     */
    protected $_model;

    /**
     * Resource model name
     *
     * @var string
     */
    protected $_resourceModel;

    /**
     * Resource instance
     *
     * @var Mage_Core_Model_Mysql4_Abstract
     */
    protected $_resource;

    /**
     * Store joined tables here
     *
     * @var array
     */
    protected $_joinedTables = array();

    /**
     * Collection constructor
     *
     * @param Mage_Core_Model_Mysql4_Abstract $resource
     */
    public function __construct($resource=null)
    {
        parent::__construct();
        $this->_construct();
        $this->_resource = $resource;
        $this->setConnection($this->getResource()->getReadConnection());
        $this->_initSelect();
    }

    /**
     * Initialization here
     *
     */
    protected function _construct()
    {

    }

    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()));
        return $this;
    }

    /**
     * Standard resource collection initalization
     *
     * @param string $model
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected function _init($model, $resourceModel=null)
    {
        $this->setModel($model);
        if (is_null($resourceModel)) {
            $resourceModel = $model;
        }
        $this->setResourceModel($resourceModel);
        return $this;
    }

    /**
     * Set model name for collection items
     *
     * @param string $model
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function setModel($model)
    {
        if (is_string($model)) {
            $this->_model = $model;
            $this->setItemObjectClass(Mage::getConfig()->getModelClassName($model));
        }
        return $this;
    }

    /**
     * Get model instance
     *
     * @param array $args
     * @return Varien_Object
     */
    public function getModelName($args=array())
    {
        return $this->_model;
    }

    public function setResourceModel($model)
    {
        $this->_resourceModel = $model;
    }

    public function getResourceModelName()
    {
        return $this->_resourceModel;
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function getResource()
    {
        if (empty($this->_resource)) {
            $this->_resource = Mage::getResourceModel($this->getResourceModelName());
        }
        return $this->_resource;
    }

    public function getTable($table)
    {
        return $this->getResource()->getTable($table);
    }

    /**
     * Retrive all ids for collection
     *
     * @return array
     */
    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->from(null,
            'main_table.' . $this->getResource()->getIdFieldName()
        );
        return $this->getConnection()->fetchCol($idsSelect);
    }

    public function join($table, $cond, $cols='*')
    {
        if (!isset($this->_joinedTables[$table])) {
            $this->getSelect()->join(array($table=>$this->getTable($table)), $cond, $cols);
            $this->_joinedTables[$table] = true;
        }
        return $this;
    }

    /**
     * Load data
     *
     * @return  Varien_Data_Collection_Db
     */
    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        foreach ($this->_items as $item) {
            $item->setOrigData();
        }
        return $this;
    }

    /**
     * Save all the entities in the collection
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function save()
    {
        foreach ($this->getItems() as $item) {
            $item->save();
        }
        return $this;
    }

    protected function _canUseCache()
    {
        return Mage::app()->useCache('collections');
    }
}