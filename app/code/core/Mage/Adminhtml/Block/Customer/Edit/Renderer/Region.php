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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer address region field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Renderer_Region extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_factory;

    /**
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
    }

    /**
     * Output the region element and javasctipt that makes it dependent from country element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $country = $element->getForm()->getElement('country_id');
        if (is_null($country)) {
            return $element->getDefaultHtml();
        }

        $regionId = $element->getForm()->getElement('region_id')->getValue();
        $quoteStoreId = $element->getEntityAttribute()->getStoreId();

        $html = '<tr>';
        $element->setClass('input-text');
        $element->setRequired(true);
        $html .= '<td class="label">' . $element->getLabelHtml() . '</td><td class="value">';
        $html .= $element->getElementHtml();

        $selectName = str_replace('region', 'region_id', $element->getName());
        $selectId = $element->getHtmlId() . '_id';
        $html .= '<select id="' . $selectId . '" name="' . $selectName
            . '" class="select required-entry" style="display:none">';
        $html .= '<option value="">' . $this->_factory->getHelper('customer')->__('Please select') . '</option>';
        $html .= '</select>';

        $html .= '<script type="text/javascript">' . "\n";
        $html .= '$("' . $selectId . '").setAttribute("defaultValue", "' . $regionId . '");' . "\n";
        $html .= 'new regionUpdater("' . $country->getHtmlId() . '", "' . $element->getHtmlId() . '", "' .
            $selectId . '", ' . Mage::helper('directory')->getRegionJsonByStore($quoteStoreId) . ');' . "\n";
        $html .= '</script>' . "\n";

        $html .= '</td></tr>' . "\n";

        return $html;
    }
}
