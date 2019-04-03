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
 * @category    Mage
 * @package     Mage_Rule
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract Rule entity resource collection model
 *
 * @category Mage
 * @package Mage_Rule
 * @author Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Rule_Model_Resource_Rule_Collection_Abstract
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store associated with rule entities information map
     *
     * Example:
     * array(
     *    'entity_type1' => array(
     *        'associations_table' => 'table_name',
     *        'rule_id_field'      => 'rule_id',
     *        'entity_id_field'    => 'entity_id'
     *    ),
     *    'entity_type2' => array(
     *        'associations_table' => 'table_name',
     *        'rule_id_field'      => 'rule_id',
     *        'entity_id_field'    => 'entity_id'
     *    )
     *    ....
     * )
     *
     * @var array
     */
    protected $_associatedEntitiesMap = array();

    /**
     * Quote rule environment
     *
     * @deprecated after 1.6.1.0
     *
     * @var Mage_Rule_Model_Environment
     */
    protected $_env;

    /**
     * Add website ids to rules data
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getFlag('add_websites_to_result') && $this->_items) {
            /** @var Mage_Rule_Model_Abstract $item */
            foreach ($this->_items as $item) {
                $item->afterLoad();
            }
        }

        return $this;
    }

    /**
     * Init flag for adding rule website ids to collection result
     *
     * @param bool|null $flag
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function addWebsitesToResult($flag = null)
    {
        $flag = ($flag === null) ? true : $flag;
        $this->setFlag('add_websites_to_result', $flag);
        return $this;
    }

    /**
     * Limit rules collection by specific websites
     *
     * @param int|array|Mage_Core_Model_Website $websiteId
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function addWebsiteFilter($websiteId)
    {
        $entityInfo = $this->_getAssociatedEntityInfo('website');
        if (!$this->getFlag('is_website_table_joined')) {
            $this->setFlag('is_website_table_joined', true);
            if ($websiteId instanceof Mage_Core_Model_Website) {
                $websiteId = $websiteId->getId();
            }

            $subSelect = $this->getConnection()->select()
                ->from(array('website' => $this->getTable($entityInfo['associations_table'])), '')
                ->where('website.' . $entityInfo['entity_id_field'] . ' IN (?)', $websiteId);
            $this->getSelect()->exists(
                $subSelect,
                'main_table.' . $entityInfo['rule_id_field'] . ' = website.' . $entityInfo['rule_id_field']
            );
        }
        return $this;
    }

    /**
     * Provide support for website id filter
     *
     * @param string $field
     * @param mixed $condition
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'website_ids') {
            return $this->addWebsiteFilter($condition);
        }

        parent::addFieldToFilter($field, $condition);
        return $this;
    }

   /**
    * Filter collection to only active or inactive rules
    *
    * @param int $isActive
    *
    * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
    */
    public function addIsActiveFilter($isActive = 1)
    {
        if (!$this->getFlag('is_active_filter')) {
            $this->addFieldToFilter('is_active', (int)$isActive ? 1 : 0);
            $this->setFlag('is_active_filter', true);
        }
        return $this;
    }

    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of rule's associated entity by specified entity type
     *
     * @param string $entityType
     *
     * @return array
     */
    protected function _getAssociatedEntityInfo($entityType)
    {
        if (isset($this->_associatedEntitiesMap[$entityType])) {
            return $this->_associatedEntitiesMap[$entityType];
        }

        $e = Mage::exception(
            'Mage_Core',
            Mage::helper('rule')->__('There is no information about associated entity type "%s".', $entityType)
        );
        throw $e;
    }





    /**
     * Set environment for all rules in collection
     *
     * @deprecated after 1.6.2.0
     *
     * @param Mage_Rule_Model_Environment $env
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function setEnv(Mage_Rule_Model_Environment $env = null)
    {
        $this->_env = $env;
        return $this;
    }

    /**
     * Retrieve environment for the rules in collection
     *
     * @deprecated after 1.6.2.0
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function getEnv()
    {
        return $this->_env;
    }

    /**
     * Set filter for the collection based on the environment
     *
     * @deprecated after 1.6.2.0
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function setActiveFilter()
    {
        return $this;
    }

    /**
     * Process the quote with all the rules in collection
     *
     * @deprecated after 1.6.2.0
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection_Abstract
     */
    public function process()
    {
        return $this;
    }
}
