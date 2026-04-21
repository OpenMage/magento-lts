<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model;

use Generator;

trait ConfigTrait
{
    public static function provideGetModelClassNameData(): Generator
    {
        yield 'old' => [
            'Mage_Core_Model_Config',
            'core/config',
        ];
    }
}
