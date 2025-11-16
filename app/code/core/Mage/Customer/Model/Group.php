<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer group model
 *
 * @package    Mage_Customer
 *
 * @method Mage_Customer_Model_Resource_Group _getResource()
 * @method Mage_Customer_Model_Resource_Group_Collection getCollection()
 * @method null|string getCustomerGroupCode()
 * @method Mage_Customer_Model_Resource_Group getResource()
 * @method Mage_Customer_Model_Resource_Group_Collection getResourceCollection()
 * @method $this setCustomerGroupCode(string $value)
 * @method $this setTaxClassId(int $value)
 */
class Mage_Customer_Model_Group extends Mage_Core_Model_Abstract
{
    /**
     * Xml config path for create account default group
     */
    public const XML_PATH_DEFAULT_ID       = 'customer/create_account/default_group';

    public const NOT_LOGGED_IN_ID          = 0;

    public const CUST_GROUP_ALL            = 32000;

    public const ENTITY                    = 'customer_group';

    public const GROUP_CODE_MAX_LENGTH     = 32;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'customer_group';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'object';

    protected static $_taxClassIds = [];

    protected function _construct()
    {
        $this->_init('customer/group');
    }

    /**
     * Alias for setCustomerGroupCode
     *
     * @param string $value
     * @return $this
     */
    public function setCode($value)
    {
        return $this->setCustomerGroupCode($value);
    }

    /**
     * Alias for getCustomerGroupCode
     *
     * @return string
     */
    public function getCode()
    {
        return (string) $this->getCustomerGroupCode();
    }

    /**
     * @param null|int $groupId
     * @return int
     * @SuppressWarnings("PHPMD.CamelCaseVariableName")
     */
    public function getTaxClassId($groupId = null)
    {
        if (!is_null($groupId)) {
            if (empty(self::$_taxClassIds[$groupId])) {
                $this->load($groupId);
                self::$_taxClassIds[$groupId] = $this->getData('tax_class_id');
            }

            $this->setData('tax_class_id', self::$_taxClassIds[$groupId]);
        }

        return $this->getData('tax_class_id');
    }

    /**
     * @return bool
     */
    public function usesAsDefault()
    {
        $data = Mage::getConfig()->getStoresConfigByPath(self::XML_PATH_DEFAULT_ID);
        if (in_array($this->getId(), $data)) {
            return true;
        }

        return false;
    }

    /**
     * Processing data save after transaction commit
     *
     * @return $this
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this,
            self::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE,
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _beforeSave()
    {
        $this->_prepareData();
        return parent::_beforeSave();
    }

    /**
     * Prepare customer group data
     *
     * @return $this
     */
    protected function _prepareData()
    {
        $this->setCode(
            substr($this->getCode(), 0, self::GROUP_CODE_MAX_LENGTH),
        );
        return $this;
    }
}
