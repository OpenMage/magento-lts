<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_System
 */

/**
 * Command-line options parsing class.
 */
class Mage_System_Args
{
    public $flags;
    public $filtered;

    /**
     * Get flags/named options
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Get filtered args
     * @return array
     */
    public function getFiltered()
    {
        return $this->filtered;
    }

    /**
     * Constructor
     * @param array|false $argv, if false $GLOBALS['argv'] is taken
     * @return void
     */
    public function __construct($argv = false)
    {
        $this->flags = [];
        $this->filtered = [];

        if (false === $argv) {
            $argv = $GLOBALS['argv'];
            array_shift($argv);
        }

        for ($i = 0, $iCount = count($argv); $i < $iCount; $i++) {
            $str = $argv[$i];

            // --foo
            if (strlen($str) > 2 && substr($str, 0, 2) == '--') {
                $str = substr($str, 2);
                $parts = explode('=', $str);
                $this->flags[$parts[0]] = true;

                // Does not have an =, so choose the next arg as its value
                if (count($parts) == 1 && isset($argv[$i + 1]) && preg_match('/^--?.+/', $argv[$i + 1]) == 0) {
                    $this->flags[$parts[0]] = $argv[$i + 1];
                    $argv[$i + 1] = null;
                } elseif (count($parts) == 2) { // Has a =, so pick the second piece
                    $this->flags[$parts[0]] = $parts[1];
                }
            } elseif (strlen($str) == 2 && $str[0] == '-') { // -a
                $this->flags[$str[1]] = true;
                if (isset($argv[$i + 1]) && preg_match('/^--?.+/', $argv[$i + 1]) == 0) {
                    $this->flags[$str[1]] = $argv[$i + 1];
                    $argv[$i + 1] = null;
                }
            } elseif (!is_null($str)) {
                $this->filtered[] = $str;
            }
        }
    }
}
