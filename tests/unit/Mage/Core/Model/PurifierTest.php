<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use Mage;
use Mage_Core_Model_Purifier as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\PurifierTrait;

final class PurifierTest extends OpenMageTest
{
    use PurifierTrait;

    /**
     * @group Model
     */
    #[DataProvider('provideGetAllowedAttributes')]
    public function testGetAllowedAttributes(?array $allowedAttributes): void
    {
        $subject = $this->getSubject([
            Subject::OPTION_ALLOWED_ATTRIBUTES => $allowedAttributes,
        ]);

        self::assertSame(gettype($allowedAttributes), gettype($subject->getAllowedAttributes()));
    }

    /**
     * @group Model
     */
    #[DataProvider('provideGetAllowedElements')]
    public function testGetAllowedElements(?array $allowedElements): void
    {
        $subject = $this->getSubject([
            Subject::OPTION_ALLOWED_ELEMENTS => $allowedElements,
        ]);

        self::assertSame(gettype($allowedElements), gettype($subject->getAllowedElements()));
    }

    /**
     * @group Model
     */
    #[DataProvider('provideGetAllowedClasses')]
    public function testGetAllowedClasses(?array $allowedClasses): void
    {
        $subject = $this->getSubject([
            Subject::OPTION_ALLOWED_CLASSES => $allowedClasses,
        ]);

        self::assertSame(gettype($allowedClasses), gettype($subject->getAllowedClasses()));
    }

    /**
     * @group Model
     */
    #[DataProvider('provideGetAllowedStyleProperties')]
    public function testGetAllowedStyleProperties(?array $allowedStyleProperties): void
    {
        $subject = $this->getSubject([
            Subject::OPTION_ALLOWED_STYLE_PROPERTIES => $allowedStyleProperties,
        ]);

        self::assertSame(gettype($allowedStyleProperties), gettype($subject->getAllowedStyleProperties()));
    }

    /**
     * @group Model
     */
    #[DataProvider('provideGetEscapeInvalidTags')]
    public function testGetEscapeInvalidTags($escapeInvalidTags): void
    {
        $subject = $this->getSubject([
            Subject::OPTION_ESCAPE_INVALID_TAGS => $escapeInvalidTags,
        ]);

        self::assertSame(gettype($escapeInvalidTags), gettype($subject->getEscapeInvalidTags()));
    }

    /**
     * @group Model
     */
    #[DataProvider('providePurify')]
    public function testPurify(string $expected, string $input, array $options = []): void
    {
        $subject = $this->getSubject($options);
        self::assertSame($expected, $subject->purify($input));
    }

    /**
     * @param array<string, mixed>|array<string, mixed[]>|null[] $options
     */
    private function getSubject(array $options): Subject
    {
        /** @var Subject $subject */
        $subject = Mage::getModel('core/purifier', $options);
        return $subject;
    }
}
