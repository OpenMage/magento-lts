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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eav Form Type Model
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Eav_Model_Resource_Form_Type _getResource()
 * @method Mage_Eav_Model_Resource_Form_Type getResource()
 * @method Mage_Eav_Model_Resource_Form_Type_Collection getCollection()
 * @method string getCode()
 * @method $this setCode(string $value)
 * @method string getLabel()
 * @method $this setLabel(string $value)
 * @method int getIsSystem()
 * @method $this setIsSystem(int $value)
 * @method string getTheme()
 * @method $this setTheme(string $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 */
class Mage_Eav_Model_Form_Type extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'eav_form_type';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('eav/form_type');
    }

    /**
     * Retrieve assigned Eav Entity types
     *
     * @return array
     */
    public function getEntityTypes()
    {
        if (!$this->hasData('entity_types')) {
            $this->setData('entity_types', $this->_getResource()->getEntityTypes($this));
        }
        return $this->_getData('entity_types');
    }

    /**
     * Set assigned Eav Entity types
     *
     * @param array $entityTypes
     * @return $this
     */
    public function setEntityTypes(array $entityTypes)
    {
        $this->setData('entity_types', $entityTypes);
        return $this;
    }

    /**
     * Assign Entity Type to Form Type
     *
     * @param int $entityTypeId
     * @return $this
     */
    public function addEntityType($entityTypeId)
    {
        $entityTypes = $this->getEntityTypes();
        if (!empty($entityTypeId) && !in_array($entityTypeId, $entityTypes)) {
            $entityTypes[] = $entityTypeId;
            $this->setEntityTypes($entityTypes);
        }
        return $this;
    }

    /**
     * Copy Form Type properties from skeleton form type
     *
     * @param Mage_Eav_Model_Form_Type $skeleton
     * @return $this
     */
    public function createFromSkeleton(Mage_Eav_Model_Form_Type $skeleton)
    {
        $fieldsetCollection = Mage::getModel('eav/form_fieldset')->getCollection()
            ->addTypeFilter($skeleton)
            ->setSortOrder();
        $elementCollection = Mage::getModel('eav/form_element')->getCollection()
            ->addTypeFilter($skeleton)
            ->setSortOrder();

        // copy fieldsets
        $fieldsetMap = [];
        foreach ($fieldsetCollection as $skeletonFieldset) {
            /** @var Mage_Eav_Model_Form_Fieldset $skeletonFieldset */
            $fieldset = Mage::getModel('eav/form_fieldset');
            $fieldset->setTypeId($this->getId())
                ->setCode($skeletonFieldset->getCode())
                ->setLabels($skeletonFieldset->getLabels())
                ->setSortOrder($skeletonFieldset->getSortOrder())
                ->save();
            $fieldsetMap[$skeletonFieldset->getId()] = $fieldset->getId();
        }

        // copy elements
        foreach ($elementCollection as $skeletonElement) {
            /** @var Mage_Eav_Model_Form_Element $skeletonElement */
            $element = Mage::getModel('eav/form_element');
            $fieldsetId = null;
            if ($skeletonElement->getFieldsetId()) {
                $fieldsetId = $fieldsetMap[$skeletonElement->getFieldsetId()];
            }
            $element->setTypeId($this->getId())
                ->setFieldsetId($fieldsetId)
                ->setAttributeId($skeletonElement->getAttributeId())
                ->setSortOrder($skeletonElement->getSortOrder());
        }

        return $this;
    }
}
