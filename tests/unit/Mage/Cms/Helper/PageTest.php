<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @dataProvider provideGetUsedInStoreConfigPaths
 * @group Mage_Cms
 * @group Mage_Cms_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Helper;

use Mage;
use Mage_Cms_Helper_Page as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\CmsTrait;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    use CmsTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('cms/page');
    }


    public function testGetUsedInStoreConfigPaths(array $expectedResult, ?array $path): void
    {
        $this->assertSame($expectedResult, Subject::getUsedInStoreConfigPaths($path));
    }
}
