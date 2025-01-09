<?php
/**
 * @category   Unserialize
 * @package    Unserialize_Parser
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Unserialize_Parser
 */
class Unserialize_Parser
{
    const TYPE_STRING = 's';
    const TYPE_INT = 'i';
    const TYPE_DOUBLE = 'd';
    const TYPE_ARRAY = 'a';
    const TYPE_BOOL = 'b';
    const TYPE_NULL = 'N';

    const SYMBOL_QUOTE = '"';
    const SYMBOL_SEMICOLON = ';';
    const SYMBOL_COLON = ':';

    /**
     * @param $str
     * @return array|null
     * @throws Exception
     */
    public function unserialize($str)
    {
        $reader = new Unserialize_Reader_Arr();
        $prevChar = null;
        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];
            $arr = $reader->read($char, $prevChar);
            if (!is_null($arr)) {
                return $arr;
            }
            $prevChar = $char;
        }
        throw new Exception('Error during unserialization');
    }
}
