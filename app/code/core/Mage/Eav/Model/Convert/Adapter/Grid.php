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
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Eav_Model_Convert_Adapter_Grid
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method $this setExceptionLocation(string $string)
 */
class Mage_Eav_Model_Convert_Adapter_Grid extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    protected $_entity;

    /**
     * @var string
     */
    protected $_entityType;

    /**
     * @return Mage_Eav_Model_Entity_Interface
     */
    public function getEntity()
    {
        if (!$this->_entityType) {
            if (!($entityType = $this->getVar('entity_type'))
                || !(($entity = Mage::getResourceSingleton($entityType)) instanceof Mage_Eav_Model_Entity_Interface)) {
                $this->addException(Mage::helper('eav')->__('Invalid entity specified'), Varien_Convert_Exception::FATAL);
            }
            $this->_entity = $entity;
        }
        return $this->_entity;
    }

    /**
     * @return $this
     */
    public function load()
    {
        try {
            $collection = Mage::getResourceModel($this->getEntity() . '_collection');
            $collection->load();
        } catch (Exception $e) {
            $this->addException(Mage::helper('eav')->__('An error occurred while loading the collection, aborting. Error: %s', $e->getMessage()), Varien_Convert_Exception::FATAL);
        }

        $data = [];
        foreach ($collection->getIterator() as $entity) {
            $data[] = $entity->getData();
        }
        $this->setData($data);
        return $this;
    }

    /**
     * @return $this
     */
    public function save()
    {
        foreach ($this->getData() as $i => $row) {
            $this->setExceptionLocation('Line: ' . $i);
            $entity = Mage::getResourceModel($this->getEntity());
            if (!empty($row['entity_id'])) {
                try {
                    $entity->load($row['entity_id']);
                    $this->setPosition('Line: ' . $i . (isset($row['entity_id']) ? ', entity_id: ' . $row['entity_id'] : ''));
                } catch (Exception $e) {
                    $this->addException(Mage::helper('eav')->__('An error occurred while loading a record, aborting. Error: %s', $e->getMessage()), Varien_Convert_Exception::FATAL);
                }
                if (!$entity->getId()) {
                    $this->addException(Mage::helper('eav')->__('Invalid entity_id, skipping the record.'), Varien_Convert_Exception::ERROR);
                    continue;
                }
            }
            try {
                $entity->addData($row)->save();
            } catch (Exception $e) {
                $this->addException(Mage::helper('eav')->__('An error occurred while saving a record, aborting. Error: ', $e->getMessage()), Varien_Convert_Exception::FATAL);
            }
        }
        return $this;
    }
}
