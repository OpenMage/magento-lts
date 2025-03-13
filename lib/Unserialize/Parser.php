<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
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
