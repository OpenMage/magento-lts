<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Eav Form Type Model
 *
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Form_Type            _getResource()
 * @method string                                       getCode()
 * @method Mage_Eav_Model_Resource_Form_Type_Collection getCollection()
 * @method int                                          getIsSystem()
 * @method string                                       getLabel()
 * @method Mage_Eav_Model_Resource_Form_Type            getResource()
 * @method Mage_Eav_Model_Resource_Form_Type_Collection getResourceCollection()
 * @method int                                          getStoreId()
 * @method string                                       getTheme()
 * @method $this                                        setCode(string $value)
 * @method $this                                        setIsSystem(int $value)
 * @method $this                                        setLabel(string $value)
 * @method $this                                        setStoreId(int $value)
 * @method $this                                        setTheme(string $value)
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
     * @inheritDoc
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
     * @param  int   $entityTypeId
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
