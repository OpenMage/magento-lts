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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
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
            $padLength = 8;
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
            $padChar = '0';
        }
        return $padChar;
    }

    /**
     * @param string|int $id
     * @return string
     */
    public function format($id)
    {
        $result = $this->getPrefix();
        $result .= str_pad((string)$id, $this->getPadLength(), $this->getPadChar(), STR_PAD_LEFT);
        return $result;
    }

    /**
     * @param string $id
     * @return string
     */
    public function frontendFormat($id)
    {
        return $id;
    }
}
