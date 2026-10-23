<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Model\Email;

use Mage;
use Mage_Adminhtml_Model_Email_PathValidator as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\Email\PathValidatorTrait;

final class PathValidatorTest extends OpenMageTest
{
    use PathValidatorTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('adminhtml/email_pathValidator');
    }

    /**
     * @dataProvider provideEncryptedConfigPaths
     * @group Model
     */
    public function testEncryptedConfigPathsAreBlocked(string $path): void
    {
        self::assertTrue(self::$subject->isValid([$path]));
    }

    /**
     * @dataProvider provideUnencryptedConfigPaths
     * @group Model
     */
    public function testUnencryptedConfigPathsAreAllowed(string $path): void
    {
        self::assertFalse(self::$subject->isValid([$path]));
    }
}
