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
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export edit block
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Block_Adminhtml_Export_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->removeButton('back')
            ->removeButton('reset')
            ->removeButton('save');
    }

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId   = 'export_id';
        $this->_blockGroup = 'importexport';
        $this->_controller = 'adminhtml_export';
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('importexport')->__('Export');
    }
}
