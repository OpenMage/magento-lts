<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Eav
 */

/**
 * EAV Form Attribute Resource Model
 *
 * @category   Mage
 * @package    Mage_Eav
 */
abstract class Mage_Eav_Model_Resource_Form_Attribute extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Return form attribute IDs by form code
     *
     * @param string $formCode
     * @return array
     */
    public function getFormAttributeIds($formCode)
    {
        $bind   = ['form_code' => $formCode];
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'attribute_id')
            ->where('form_code = :form_code');

        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }
}
