<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Customer address region field renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Renderer_Region extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    public function __construct(array $args = [])
    {
        $this->_factory = empty($args['factory']) ? Mage::getSingleton('core/factory') : $args['factory'];
    }

    /**
     * Output the region element and javasctipt that makes it dependent from country element
     *
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
        $html .= 'new regionUpdater("' . $country->getHtmlId() . '", "' . $element->getHtmlId() . '", "'
            . $selectId . '", ' . Mage::helper('directory')->getRegionJsonByStore($quoteStoreId) . ');' . "\n";
        $html .= '</script>' . "\n";

        return $html . ('</td></tr>' . "\n");
    }
}
