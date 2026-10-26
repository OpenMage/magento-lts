<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage
 */

/**
 * Disable the phar stream wrapper to prevent phar deserialization attacks.
 * Functions like file_exists(), getimagesize(), etc. can trigger deserialization
 * when processing phar:// paths, potentially leading to remote code execution.
 *
 * @link https://blog.ripstech.com/2018/new-php-exploitation-technique/
 */
if (in_array('phar', stream_get_wrappers(), true)) {
    stream_wrapper_unregister('phar');
}

/**
 * Apply workaround for the libxml PHP bugs:
 * @link https://bugs.php.net/bug.php?id=62577
 * @link https://bugs.php.net/bug.php?id=64938
 */
if ((LIBXML_VERSION < 20900) && function_exists('libxml_disable_entity_loader')) {
    libxml_disable_entity_loader(false);
}
