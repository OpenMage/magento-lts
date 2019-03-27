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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer image file field form xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Form_Renderer_Image extends Mage_XmlConnect_Block_Customer_Form_Renderer_File
{
    /**
     * Field type
     *
     * @var string
     */
    protected $_filedType = 'image';

    /**
     * Add validator for image file field to fieldset xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract $fieldXmlObj
     * @return Mage_XmlConnect_Block_Customer_Form_Renderer_Image
     */
    protected function _addValidator(Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract $fieldXmlObj)
    {
        parent::_addValidator($fieldXmlObj);

        $validateRules = $this->getAttributeObject()->getValidateRules();

        if (!empty($validateRules)) {

            foreach ($fieldXmlObj->getElements() as $element) {
                if ($element->getType() == 'validator') {
                    $validatorXmlObj = $element;
                }
            }

            if (!isset($validatorXmlObj)) {
                $validatorXmlObj = $fieldXmlObj->addValidator();
            }

            if (!empty($validateRules['max_image_width'])) {
                $minTextLength = (int) $validateRules['max_image_width'];
                $validatorXmlObj->addRule(array(
                    'type' => 'max_image_width', 'value' => $minTextLength, 'field_label' => $this->getLabel()
                ));
            }

            if (!empty($validateRules['max_image_heght'])) {
                $maxTextLength = $validateRules['max_image_heght'];
                $validatorXmlObj->addRule(array(
                    'type' => 'max_image_height', 'value' => $maxTextLength, 'field_label' => $this->getLabel()
                ));
            }
        }
        return $this;
    }
}
