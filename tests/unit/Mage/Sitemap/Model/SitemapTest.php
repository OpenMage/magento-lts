<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sitemap\Model;

use Mage_Sitemap_Model_Sitemap as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sitemap\SitemapTrait;
use Throwable;

class SitemapTest extends OpenMageTest
{
    use SitemapTrait;

    /**
     * @dataProvider provideGetPreparedFilenameData
     * @group Model
     */
    public function testGetPreparedFilename(array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertIsString($mock->getPreparedFilename());
    }

    /**
     * @dataProvider provideGenerateXmlData
     * @group Model
     * @throws Throwable
     * @todo  test validation
     * @todo  test content of xml
     */
    public function testGenerateXml(array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);
        static::assertInstanceOf(Subject::class, $mock);

        $result = $mock->generateXml();
        static::assertInstanceOf(Subject::class, $result);

        /** @var string $file */
        $file = $methods['getSitemapFilename'];
        static::assertFileExists($file);
        unlink($file);
    }
}
