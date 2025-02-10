<?php
/**
 * Defined by Zend_Filter_Interface
Returns the string $value, removing all but alphabetic (including -_;) and digit characters
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @param  string $value
 * @return string
 */
/**
 * @package    Varien_Filter
 */

class Varien_Filter_FormElementName extends Zend_Filter_Alnum
{
    
    public function filter($value)
    {
        $whiteSpace = $this->allowWhiteSpace ? '\s' : '';
        if (!self::$_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z0-9 match
            $pattern = '/[^a-zA-Z0-9\[\];_\-' . $whiteSpace . ']/';
        } elseif (self::$_meansEnglishAlphabet) {
            //The Alphabet means english alphabet.
            $pattern = '/[^a-zA-Z0-9\[\];_\-' . $whiteSpace . ']/u';
        } else {
            //The Alphabet means each language's alphabet.
            $pattern = '/[^\p{L}\p{N}\[\];_\-' . $whiteSpace . ']/u';
        }
        return preg_replace($pattern, '', (string) $value);
    }
}
