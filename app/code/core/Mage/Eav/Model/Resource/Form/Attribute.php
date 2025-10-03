<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * EAV Form Attribute Resource Model
 *
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
