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
 * @package    Mage_Widget
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Template Filter Model
 *
 * @category   Mage
 * @package    Mage_Widget
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Model_Template_Filter extends Mage_Cms_Model_Template_Filter
{
    /**
     * Generate widget
     *
     * @param array $construction
     * @return string
     */
    public function widgetDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);

        // Determine what name block should have in layout
        $name = $params['name'] ?? null;

        // validate required parameter type or id
        if (!empty($params['type'])) {
            $type = $params['type'];
        } elseif (!empty($params['id'])) {
            $preconfigured = Mage::getResourceSingleton('widget/widget')
                ->loadPreconfiguredWidget($params['id']);
            $type = $preconfigured['widget_type'];
            $params = $preconfigured['parameters'];
        } else {
            return '';
        }

        // we have no other way to avoid fatal errors for type like 'cms/widget__link', '_cms/widget_link' etc.
        $xml = Mage::getSingleton('widget/widget')->getXmlElementByType($type);
        if ($xml === null) {
            return '';
        }

        // define widget block and check the type is instance of Widget Interface
        $widget = Mage::app()->getLayout()->createBlock($type, $name, $params);
        if (!$widget instanceof Mage_Widget_Block_Interface) {
            return '';
        }

        return $widget->toHtml();
    }
}
