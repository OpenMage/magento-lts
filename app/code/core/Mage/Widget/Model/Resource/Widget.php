<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Widget
 */

/**
 * Preconfigured widget
 *
 * @category   Mage
 * @package    Mage_Widget
 */
class Mage_Widget_Model_Resource_Widget extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('widget/widget', 'widget_id');
    }

    /**
     * Retrieves pre-configured parameters for widget
     *
     * @param int $widgetId
     * @return array|false
     */
    public function loadPreconfiguredWidget($widgetId)
    {
        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
            ->from($this->getMainTable())
            ->where($this->getIdFieldName() . '=:' . $this->getIdFieldName());
        $bind = [$this->getIdFieldName() => $widgetId];
        $widget = $readAdapter->fetchRow($select, $bind);
        if (is_array($widget)) {
            if ($widget['parameters']) {
                $widget['parameters'] = unserialize($widget['parameters'], ['allowed_classes' => false]);
            }
            return $widget;
        }
        return false;
    }
}
