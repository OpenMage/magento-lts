<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Recurring\Profile;

use Mage;
use Mage_Core_Model_Session;
use Mage_Sales_Block_Recurring_Profile_View as Subject;
use Mage_Sales_Model_Recurring_Profile;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Block\Recurring\Profile\ViewTrait;
use Override;
use ReflectionProperty;

final class ViewTest extends OpenMageTest
{
    use ViewTrait;

    private const FORM_KEY = 'TestFormKey123';

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    #[Override]
    protected function setUp(): void
    {
        $coreSession = $this->createPartialMock(Mage_Core_Model_Session::class, ['getFormKey']);
        $coreSession->method('getFormKey')->willReturn(self::FORM_KEY);
        Mage::unregister('_singleton/core/session');
        Mage::register('_singleton/core/session', $coreSession);
    }

    #[Override]
    protected function tearDown(): void
    {
        Mage::unregister('_singleton/core/session');
    }

    /**
     * Every state changing URL must carry the session form key, the controller rejects requests
     * without it (GHSA-qfwh-wv4p-95qv). Themes still navigating to them with a GET rely on it.
     *
     * @group Block
     */
    public function testActionUrlsCarryFormKey(): void
    {
        $profile = $this->getMockWithCalledMethods(Mage_Sales_Model_Recurring_Profile::class, [
            'canCancel' => true,
            'canSuspend' => true,
            'canActivate' => true,
            'canFetchUpdate' => true,
            'getId' => 7,
        ]);

        $subject = new Subject();
        (new ReflectionProperty(Subject::class, '_profile'))->setValue($subject, $profile);
        $subject->prepareViewData();

        foreach (['cancel' => 'cancel_url', 'suspend' => 'suspend_url', 'activate' => 'activate_url'] as $action => $key) {
            $url = $subject->getData($key);
            self::assertIsString($url);
            self::assertStringContainsString('/profile/7/', $url);
            self::assertStringContainsString('/action/' . $action . '/', $url);
            self::assertStringContainsString('/form_key/' . self::FORM_KEY . '/', $url);
        }

        $url = $subject->getData('update_url');
        self::assertIsString($url);
        self::assertStringContainsString('/profile/7/', $url);
        self::assertStringContainsString('/form_key/' . self::FORM_KEY . '/', $url);
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
