<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Convert parser interface
 *
 * @package    Mage_Dataflow
 */
interface Mage_Dataflow_Model_Convert_Parser_Interface
{
    public function parse();

    public function unparse();
}
