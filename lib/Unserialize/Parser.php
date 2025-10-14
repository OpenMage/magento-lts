<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Unserialize_Parser
 */

/**
 * Class Unserialize_Parser
 */
class Unserialize_Parser
{
    public const TYPE_STRING = 's';

    public const TYPE_INT = 'i';

    public const TYPE_DOUBLE = 'd';

    public const TYPE_ARRAY = 'a';

    public const TYPE_BOOL = 'b';

    public const TYPE_NULL = 'N';

    public const SYMBOL_QUOTE = '"';

    public const SYMBOL_SEMICOLON = ';';

    public const SYMBOL_COLON = ':';

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
