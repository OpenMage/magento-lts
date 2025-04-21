<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

/**
 * Convert parser interface
 *
 * @package    Varien_Convert
 */
interface Varien_Convert_Parser_Interface
{
    public function parse();

    public function unparse();
}
