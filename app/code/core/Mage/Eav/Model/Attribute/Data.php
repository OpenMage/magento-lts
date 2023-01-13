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
 * EAV Entity Attribute Data Factory
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Attribute_Data
{
    public const OUTPUT_FORMAT_JSON    = 'json';
    public const OUTPUT_FORMAT_TEXT    = 'text';
    public const OUTPUT_FORMAT_HTML    = 'html';
    public const OUTPUT_FORMAT_PDF     = 'pdf';
    public const OUTPUT_FORMAT_ONELINE = 'oneline';
    public const OUTPUT_FORMAT_ARRAY   = 'array'; // available only for multiply attributes

    /**
     * Array of attribute data models by input type
     *
     * @var array
     */
    protected static $_dataModels   = [];

    /**
     * Return attribute data model by attribute
     * Set entity to data model (need for work)
     *
     * @param Mage_Eav_Model_Attribute $attribute
     * @param Mage_Core_Model_Abstract $entity
     * @return Mage_Eav_Model_Attribute_Data_Abstract
     */
    public static function factory(Mage_Eav_Model_Attribute $attribute, Mage_Core_Model_Abstract $entity)
    {
        $dataModelClass = $attribute->getDataModel();
        if (!empty($dataModelClass)) {
            if (empty(self::$_dataModels[$dataModelClass])) {
                /** @var Mage_Eav_Model_Attribute_Data_Abstract $dataModel */
                $dataModel = Mage::getModel($dataModelClass);
                self::$_dataModels[$dataModelClass] = $dataModel;
            } else {
                $dataModel = self::$_dataModels[$dataModelClass];
            }
        } else {
            if (empty(self::$_dataModels[$attribute->getFrontendInput()])) {
                $dataModelClass = sprintf('eav/attribute_data_%s', $attribute->getFrontendInput());
                /** @var Mage_Eav_Model_Attribute_Data_Abstract $dataModel */
                $dataModel      = Mage::getModel($dataModelClass);
                self::$_dataModels[$attribute->getFrontendInput()] = $dataModel;
            } else {
                $dataModel = self::$_dataModels[$attribute->getFrontendInput()];
            }
        }

        $dataModel->setAttribute($attribute);
        $dataModel->setEntity($entity);

        return $dataModel;
    }
}
