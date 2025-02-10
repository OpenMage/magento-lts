<?php
/**
 * Convert parser interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Dataflow
 */
interface Mage_Dataflow_Model_Convert_Parser_Interface
{
    public function parse();

    public function unparse();
}
