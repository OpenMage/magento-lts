<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Email\Template;

use Mage_Core_Model_Email_Template_Abstract as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Email\Template\AbstractTrait;

final class AbstractTest extends OpenMageTest
{
    use AbstractTrait;

    private static Subject $subject;

    protected function setUp(): void
    {
        self::$subject = $this->getMockBuilder(Subject::class)->getMock();
    }

    /**
     * @dataProvider provideValidateFileExension
     * @group Model
     */
    public function testValidateFileExension(bool $expectedResult, string $filePath, string $extension, bool $fileExists): void
    {
        if ($fileExists) {
            self::assertFileExists($filePath);
        } else {
            self::assertFileDoesNotExist($filePath);
        }

        self::assertSame($expectedResult, self::$subject->validateFileExension($filePath, $extension));
    }
}
