<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Entity attribute backend interface
 *
 * Backend is responsible for saving the values of the attribute
 * and performing pre and post actions
 *
 * @package    Mage_Eav
 */
interface Mage_Eav_Model_Entity_Attribute_Backend_Interface
{
    public function getTable();

    public function isStatic();

    public function getType();

    public function getEntityIdField();

    /**
     * @param  int   $valueId
     * @return $this
     */
    public function setValueId($valueId);

    public function getValueId();

    /**
     * @param  object $object
     * @return mixed
     */
    public function afterLoad($object);

    /**
     * @param  object $object
     * @return mixed
     */
    public function beforeSave($object);

    /**
     * @param  object $object
     * @return mixed
     */
    public function afterSave($object);

    /**
     * @param  object $object
     * @return mixed
     */
    public function beforeDelete($object);

    /**
     * @param  object $object
     * @return mixed
     */
    public function afterDelete($object);

    /**
     * Get entity value id
     *
     * @param Varien_Object $entity
     */
    public function getEntityValueId($entity);

    /**
     * Set entity value id
     *
     * @param Varien_Object $entity
     * @param int           $valueId
     */
    public function setEntityValueId($entity, $valueId);
}
