<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Dataflow\Model\Convert\Parser;

use Generator;
use Mage;

trait AbstractTrait
{
    public function provideGetCopyFile(): Generator
    {
        $prefix = Mage::app()->getConfig()->getTempVarDir() . '/import/';
        $string = 'test';

        $tests = [
            'simple' => '../../../',
            'single bypass' => '....//....//....//./',
            'nested bypass' => '..././..././..././',
            'mixed bypass' => '..././....//..././',
        ];

        foreach ($tests as $testName => $path) {
            yield $testName => [
                $prefix . $string,
                $path . $string,
            ];
        }

        $path = '..%2F..%2F..%2Ftest';
        yield 'url bypass' => [
            $prefix . $string,
            $path,
        ];
    }
}
