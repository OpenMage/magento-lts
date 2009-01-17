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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * REgion field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Customer_Renderer_Region implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Country region collections
     *
     * array(
     *      [$countryId] => Varien_Data_Collection_Db
     * )
     *
     * @var array
     */
    static protected $_regionCollections;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr>'."\n";

        $countryId = false;
        if ($country = $element->getForm()->getElement('country_id')) {
            $countryId = $country->getValue();
        }

        $regionCollection = false;
        if ($countryId) {
            if (!isset(self::$_regionCollections[$countryId])) {
                self::$_regionCollections[$countryId] = Mage::getModel('directory/country')
                    ->setId($countryId)
                    ->getLoadedRegionCollection();
            }
            $regionCollection = self::$_regionCollections[$countryId];
        }

        $regionId = $element->getForm()->getElement('region_id')->getValue();

        $htmlAttributes = $element->getHtmlAttributes();
        foreach ($htmlAttributes as $key => $attribute) {
            if ('type' === $attribute) {
                unset($htmlAttributes[$key]);
                break;
            }
        }
        if ($regionCollection && $regionCollection->getSize()) {
            $elementClass = $element->getClass();
            $element->setClass(str_replace('input-text', '', $elementClass));
            $html.= '<td class="label">'.$element->getLabelHtml().'</td>';
            $html.= '<td class="value"><select id="'.$element->getHtmlId().'" name="'.$element->getName().'" '
                 .$element->serialize($htmlAttributes).'>'."\n";
            foreach ($regionCollection as $region) {
                $selected = ($regionId==$region->getId()) ? ' selected="selected"' : '';
            	$html.= '<option value="'.$region->getId().'"'.$selected.'>'.$region->getName().'</option>';
            }
            $html.= '</select></td>';
            $element->setClass($elementClass);
        }
        else {
            $element->setClass('input-text');
            $html.= '<td class="label"><label for="'.$element->getHtmlId().'">'
                . $element->getLabel()
                . ' <span class="required" style="display:none">*</span></label></td>';

            $element->setRequired(false);
            $html.= '<td class="value"><input id="'.$element->getHtmlId().'" name="'.$element->getName()
                 .'" value="'.$element->getEscapedValue().'"'.$element->serialize($htmlAttributes).'/></td>'."\n";
        }
        $html.= '</tr>'."\n";
        return $html;
    }
}
