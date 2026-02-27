<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Purifier as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\PurifierTrait;

final class PurifierTest extends OpenMageTest
{
    use PurifierTrait;

    /**
     * @dataProvider provideGetAllowedAttributes
     * @group Model
     */
    public function testGetAllowedAttributes(?array $allowedAttributes): void
    {
        $subject = $this->getSubject([
            Subject::OPTION_ALLOWED_ATTRIBUTES => $allowedAttributes,
        ]);

        self::assertSame(gettype($allowedAttributes), gettype($subject->getAllowedAttributes()));
    }

    /**
     * @dataProvider provideGetAllowedElements
     * @group Model
     */
    public function testGetAllowedElements(?array $allowedElements): void
    {
        $subject = $this->getSubject([
            Subject::OPTION_ALLOWED_ELEMENTS => $allowedElements,
        ]);

        self::assertSame(gettype($allowedElements), gettype($subject->getAllowedElements()));
    }

    /**
     * @dataProvider provideGetAllowedClasses
     * @group Model
     */
    public function testGetAllowedClasses(?array $allowedClasses): void
    {
        $subject = $this->getSubject([
            Subject::OPTION_ALLOWED_CLASSES => $allowedClasses,
        ]);

        self::assertSame(gettype($allowedClasses), gettype($subject->getAllowedClasses()));
    }

    /**
     * @dataProvider provideGetAllowedStyleProperties
     * @group Model
     */
    public function testGetAllowedStyleProperties(?array $allowedStyleProperties): void
    {
        $subject = $this->getSubject([
            Subject::OPTION_ALLOWED_STYLE_PROPERTIES => $allowedStyleProperties,
        ]);

        self::assertSame(gettype($allowedStyleProperties), gettype($subject->getAllowedStyleProperties()));
    }

    /**
     * @dataProvider provideGetEscapeInvalidTags
     * @group Model
     */
    public function testGetters($escapeInvalidTags): void
    {
        $subject = $this->getSubject([
            Subject::OPTION_ESCAPE_INVALID_TAGS => $escapeInvalidTags,
        ]);

        self::assertSame(gettype($escapeInvalidTags), gettype($subject->getEscapeInvalidTags()));
    }

    /**
     * @dataProvider providePurify
     * @group Model
     */
    public function testPurify(string $expected, string $input, array $options = []): void
    {
        $subject = $this->getSubject($options);
        self::assertSame($expected, $subject->purify($input));
    }

    private function getSubject(array $options): Subject
    {
        /** @var Subject $subject */
        $subject = Mage::getModel('core/purifier', $options);
        return $subject;
    }
}
