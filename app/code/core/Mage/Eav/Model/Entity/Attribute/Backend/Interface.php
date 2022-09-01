<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entity attribute backend interface
 *
 * Backend is responsible for saving the values of the attribute
 * and performing pre and post actions
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Eav_Model_Entity_Attribute_Backend_Interface
{
    public function getTable();
    public function isStatic();
    public function getType();
    public function getEntityIdField();

    /**
     * @param int $valueId
     * @return int
     */
    public function setValueId($valueId);
    public function getValueId();

    /**
     * @param object $object
     * @return mixed
     */
    public function afterLoad($object);

    /**
     * @param object $object
     * @return mixed
     */
    public function beforeSave($object);

    /**
     * @param object $object
     * @return mixed
     */
    public function afterSave($object);

    /**
     * @param object $object
     * @return mixed
     */
    public function beforeDelete($object);

    /**
     * @param object $object
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
     * @param int $valueId
     */
    public function setEntityValueId($entity, $valueId);
}
