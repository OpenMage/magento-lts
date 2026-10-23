<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core layout update resource model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Layout extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('core/layout_update', 'layout_update_id');
    }

    /**
     * Retrieve layout updates by handle
     *
     * @param  string $handle
     * @param  array  $params
     * @return string
     */
    public function fetchUpdatesByHandle($handle, $params = [])
    {
        $bind = [
            'store_id'  => Mage::app()->getStore()->getId(),
            'area'      => Mage::getSingleton('core/design_package')->getArea(),
            'package'   => Mage::getSingleton('core/design_package')->getPackageName(),
            'theme'     => Mage::getSingleton('core/design_package')->getTheme('layout'),
        ];

        foreach ($params as $key => $value) {
            if (isset($bind[$key])) {
                $bind[$key] = $value;
            }
        }

        $bind['layout_update_handle'] = $handle;
        $result = '';

        $readAdapter = $this->_getReadAdapter();
        if ($readAdapter) {
            $select = $readAdapter->select()
                ->from(['layout_update' => $this->getMainTable()], ['xml'])
                ->join(
                    ['link' => $this->getTable('core/layout_link')],
                    'link.layout_update_id=layout_update.layout_update_id',
                    '',
                )
                ->where('link.store_id IN (0, :store_id)')
                ->where('link.area = :area')
                ->where('link.package = :package')
                ->where('link.theme = :theme')
                ->where('layout_update.handle = :layout_update_handle')
                ->order('layout_update.sort_order ' . Varien_Db_Select::SQL_ASC);

            $result = implode('', $readAdapter->fetchCol($select, $bind));
        }

        return $result;
    }
}
