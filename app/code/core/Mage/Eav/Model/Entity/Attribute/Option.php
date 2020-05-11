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
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Emtity attribute option model
 *
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Option _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Attribute_Option getResource()
 * @method int getAttributeId()
 * @method Mage_Eav_Model_Entity_Attribute_Option setAttributeId(int $value)
 * @method int getSortOrder()
 * @method Mage_Eav_Model_Entity_Attribute_Option setSortOrder(int $value)
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Attribute_Option extends Mage_Core_Model_Abstract
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('eav/entity_attribute_option');
    }
}
