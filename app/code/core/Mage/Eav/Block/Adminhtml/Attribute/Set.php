<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Block_Adminhtml_Attribute_Set extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'eav';
        $this->_controller = 'adminhtml_attribute_set';
        if ($entity_type = Mage::registry('entity_type')) {
            $this->_headerText = Mage::helper('eav')->__('Manage %s Attribute Sets', Mage::helper('eav')->formatTypeCode($entity_type));
        } else {
            $this->_headerText = Mage::helper('eav')->__('Manage Attribute Sets');
        }
        $this->_addButtonLabel = Mage::helper('eav')->__('Add New Set');
        parent::__construct();
    }

    public function getCreateUrl(): string
    {
        return $this->getUrl('*/*/add');
    }

    public function getHeaderCssClass(): string
    {
        return 'icon-head head-eav-attribute-sets';
    }
}
