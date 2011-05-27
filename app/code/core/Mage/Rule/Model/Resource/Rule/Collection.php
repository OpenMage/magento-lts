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
 * @category    Mage
 * @package     Mage_Rule
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Abstract rules collection to be extended
 *
 * @category    Mage
 * @package     Mage_Rule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rule_Model_Resource_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Quote rule environment
     *
     * @var Mage_Rule_Model_Environment
     */
    protected $_env;

    /**
     * Define resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('rule/rule');
    }

    /**
     * Set environment for all rules in collection
     *
     * @param Mage_Rule_Model_Environment $env
     * @return Mage_Rule_Model_Resource_Rule_Collection
     */
    public function setEnv(Mage_Rule_Model_Environment $env = null)
    {
        $this->_env = $env;
        return $this;
    }

    /**
     * Retrieve environment for the rules in collection
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection
     */
    public function getEnv()
    {
        if (!$this->_env) {
            $this->_env = Mage::getModel('core/rule_environment');
            $this->_env->collect();
        }
        return $this->_env;
    }

    /**
     * Overload default addItem method to set environment for the rules
     *
     * @param Mage_Rule_Model_Abstract $rule
     * @return Mage_Rule_Model_Resource_Rule_Collection
     */
    public function addItem(Varien_Object $rule)
    {
        $rule->setEnv($this->getEnv())->setIsCollectionValidated(true);
        parent::addItem($rule);
        return $this;
    }

    /**
     * Set filter for the collection based on the environment
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection
     */
    public function setActiveFilter()
    {
        $e = $this->getEnv()->getData();

        $this->addFieldToFilter('is_active', 1);

        if (!empty($e['now'])) {
            if (!is_numeric($e['now'])) {
                $e['now'] = strtotime($e['now']);
            }
            $now = date("Y-m-d H:i:s", $e['now']);
        } else {
            $now = date("Y-m-d H:i:s");
        }

        $this->addFieldToFilter('start_at', array('to'=>$now));
        $this->addFieldToFilter('expire_at', array('from'=>$now));

        return $this;
    }

    /**
     * Process the quote with all the rules in collection
     *
     * @return Mage_Rule_Model_Resource_Rule_Collection
     */
    public function process()
    {
        $rules = $this->getItems();
        foreach ($rules as $rule) {
            $rule->process();
            if ($rule->getStopProcessingRules()) {
                break;
            }
        }
        return $this;
    }

    /**
     * Walk across collection after load
     *
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');
    }
}
