<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * EAV Entity Attribute Data Factory
 *
 * @package    Mage_Eav
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
        } elseif (empty(self::$_dataModels[$attribute->getFrontendInput()])) {
            $dataModelClass = sprintf('eav/attribute_data_%s', $attribute->getFrontendInput());
            /** @var Mage_Eav_Model_Attribute_Data_Abstract $dataModel */
            $dataModel      = Mage::getModel($dataModelClass);
            self::$_dataModels[$attribute->getFrontendInput()] = $dataModel;
        } else {
            $dataModel = self::$_dataModels[$attribute->getFrontendInput()];
        }

        $dataModel->setAttribute($attribute);
        $dataModel->setEntity($entity);

        return $dataModel;
    }
}
