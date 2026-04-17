<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Email\Template;

use Generator;

trait AbstractTrait
{
    public function provideValidateFileExension(): Generator
    {
        yield 'css file exists' => [
            true,
            $_SERVER['TEST_ROOT'] . '/unit/fixtures/files/test.css',
            'css',
            true,
        ];
        yield 'css file exists, but empty' => [
            false,
            $_SERVER['TEST_ROOT'] . '/unit/fixtures/files/test-empty.css',
            'css',
            true,
        ];
        yield 'css file not exists' => [
            false,
            $_SERVER['TEST_ROOT'] . '/unit/fixtures/files/test.not-exist',
            'css',
            false,
        ];
    }
}
