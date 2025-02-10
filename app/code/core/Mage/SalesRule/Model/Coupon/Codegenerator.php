<?php
/**
 * Class Mage_SalesRule_Model_Coupon_Codegenerator
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 * @method string getAlphabet()
 * @method int getLength()
 * @method int  getLengthMax()
 * @method int  getLengthMin()
 */
class Mage_SalesRule_Model_Coupon_Codegenerator extends Varien_Object implements Mage_SalesRule_Model_Coupon_CodegeneratorInterface
{
    /**
     * Retrieve generated code
     *
     * @return string
     */
    public function generateCode()
    {
        $alphabet = ($this->getAlphabet() ? $this->getAlphabet() : 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        $lengthMin = ($this->getLengthMin() ? $this->getLengthMin() : 16);
        $lengthMax = ($this->getLengthMax() ? $this->getLengthMax() : 32);
        $length = ($this->getLength() ? $this->getLength() : rand($lengthMin, $lengthMax));
        $result = '';
        $indexMax = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, $indexMax);
            $result .= $alphabet[$index];
        }
        return $result;
    }

    /**
     * Retrieve delimiter
     *
     * @return string
     */
    public function getDelimiter()
    {
        return ($this->getData('delimiter') ? $this->getData('delimiter') : '-');
    }
}
