<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * @package    Mage_Eav
 *
 * @method string getLastId()
 * @method string getPrefix()
 */
abstract class Mage_Eav_Model_Entity_Increment_Abstract extends Varien_Object implements Mage_Eav_Model_Entity_Increment_Interface
{
    /**
     * @return int
     */
    public function getPadLength()
    {
        $padLength = $this->getData('pad_length');
        if (empty($padLength)) {
            return 8;
        }

        return $padLength;
    }

    /**
     * @return string
     */
    public function getPadChar()
    {
        $padChar = $this->getData('pad_char');
        if (empty($padChar)) {
            return '0';
        }

        return $padChar;
    }

    /**
     * @param  int|string $id
     * @return string
     */
    public function format($id)
    {
        $result = $this->getPrefix();
        return $result . str_pad((string) $id, $this->getPadLength(), $this->getPadChar(), STR_PAD_LEFT);
    }

    /**
     * @param  string $id
     * @return string
     */
    public function frontendFormat($id)
    {
        return $id;
    }
}
