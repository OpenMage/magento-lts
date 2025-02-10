<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */
interface Mage_SalesRule_Model_Coupon_CodegeneratorInterface
{
    /**
     * Retrieve generated code
     *
     * @return string
     */
    public function generateCode();

    /**
     * Retrieve delimiter
     *
     * @return string
     */
    public function getDelimiter();
}
