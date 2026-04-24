<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Zend;

use Mage_Captcha_Model_Zend;
use Rector\Renaming\ValueObject\MethodCallRename;

final class Captcha
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        # update for laminas\captcha
        return [
            new MethodCallRename(Mage_Captcha_Model_Zend::class, '_generateWord', 'generateWord'),
            new MethodCallRename(Mage_Captcha_Model_Zend::class, '_setWord', 'setWord'),
            new MethodCallRename(Mage_Captcha_Model_Zend::class, '_randomSize', 'randomSize'),
            new MethodCallRename(Mage_Captcha_Model_Zend::class, '_gc', 'gc'),
        ];
    }
}
