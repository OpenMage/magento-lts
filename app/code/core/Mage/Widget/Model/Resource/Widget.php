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
 * @package     Mage_Widget
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Preconfigured widget
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Model_Resource_Widget extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('widget/widget', 'widget_id');
    }

    /**
     * Retrieves pre-configured parameters for widget
     *
     * @param int $widgetId
     * @return array
     */
    public function loadPreconfiguredWidget($widgetId)
    {
        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from($this->getMainTable())
            ->where($this->getIdFieldName() . '=:' . $this->getIdFieldName());
        $bind = array($this->getIdFieldName() => $widgetId);
        $widget = $readAdapter->fetchRow($select, $bind);
        if (is_array($widget)) {
            if ($widget['parameters']) {
                $widget['parameters'] = unserialize($widget['parameters']);
            }
            return $widget;
        }
        return false;
    }
}
