<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Recurring\Profile;

use Mage_Sales_Block_Recurring_Profile_View as Subject;
use Mage_Sales_Model_Recurring_Profile;
use Override;
use ReflectionProperty;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Block\Recurring\Profile\ViewTrait;

final class ViewTest extends OpenMageTest
{
    use ViewTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @group Block
     */
    public function testPrepareItemInfoDoesNotUnserializeObjects(): void
    {
        ViewTestFixture::$awakened = false;

        $this->prepareItemInfo(serialize(['qty' => 1, 'exploit' => new ViewTestFixture()]));

        self::assertFalse(ViewTestFixture::$awakened);
        self::assertSame([], self::$subject->getRenderedInfo());
    }

    /**
     * @group Block
     */
    public function testPrepareItemInfoWithoutOptions(): void
    {
        $this->prepareItemInfo(serialize(['qty' => 1]));

        self::assertSame([], self::$subject->getRenderedInfo());
    }

    /**
     * @group Block
     */
    public function testPrepareItemInfoIgnoresAMalformedBuyRequest(): void
    {
        $this->prepareItemInfo('not-serialized-data');

        self::assertSame([], self::$subject->getRenderedInfo());
    }

    /**
     * Run prepareItemInfo() on a profile whose order item info holds the given buy request
     */
    private function prepareItemInfo(string $buyRequest): void
    {
        $profile = $this->createPartialMock(Mage_Sales_Model_Recurring_Profile::class, ['getInfoValue']);
        $profile->method('getInfoValue')->willReturnCallback(
            static fn($infoKey, $infoValueKey) => $infoValueKey === 'info_buyRequest' ? $buyRequest : null,
        );

        (new ReflectionProperty(Subject::class, '_profile'))->setValue(self::$subject, $profile);

        self::$subject->prepareItemInfo();
    }
}
