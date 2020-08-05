<?php


/**
 * @see       https://github.com/laminas/laminas-crypt for the canonical source repository
 * @copyright https://github.com/laminas/laminas-crypt/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-crypt/blob/master/LICENSE.md New BSD License
 *
 *
 *
 * Copyright (c) 2020 Laminas Project a Series of LF Projects, LLC.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * - Neither the name of Laminas Foundation nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS
 * OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Tools for cryptography
 */
class Zend_Crypt_Utils
{
    /**
     * Compare two strings to avoid timing attacks
     *
     * C function memcmp() internally used by PHP, exits as soon as a difference
     * is found in the two buffers. That makes possible of leaking
     * timing information useful to an attacker attempting to iteratively guess
     * the unknown string (e.g. password).
     * The length will leak.
     *
     * @param string $expected
     * @param string $actual
     *
     * @return bool
     */
    public static function compareStrings($expected, $actual)
    {
        $expected = (string)$expected;
        $actual = (string)$actual;

        if (function_exists('hash_equals')) {
            return hash_equals($expected, $actual);
        }

        $lenExpected = mb_strlen($expected, '8bit');
        $lenActual = mb_strlen($actual, '8bit');
        $len = min($lenExpected, $lenActual);

        $result = 0;
        for ($i = 0; $i < $len; $i++) {
            $result |= ord($expected[$i]) ^ ord($actual[$i]);
        }
        $result |= $lenExpected ^ $lenActual;

        return ($result === 0);
    }
}
