<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien;

use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Varien\ObjectTrait;
use Varien_Exception;
use Varien_Object as Subject;

final class ObjectTest extends OpenMageTest
{
    use ObjectTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @dataProvider provideGetDataData
     * @param mixed           $expectedResult
     * @param string          $setKey
     * @param mixed           $setValue
     * @param null|int|string $index
     *
     * @group Varien_Object
     */
    public function testGetData($expectedResult, $setKey, $setValue, null|string $key, $index = null): void
    {
        self::$subject->setData($setKey, $setValue);
        self::assertSame($expectedResult, self::$subject->getData($key, $index));
    }

    /**
     * @dataProvider provideToStringData
     * @group Varien_Object
     */
    public function testToString(string $expectedResult, string $format): void
    {
        self::$subject->setString0('0');
        self::$subject->setString1('one');
        self::$subject->setString2('two');
        self::$subject->setString3('three');

        self::assertSame($expectedResult, self::$subject->toString($format));
    }

    /**
     * @group Varien_Object
     */
    public function testGetSetUnsData(): void
    {
        self::assertTrue(self::$subject->isEmpty());
        self::$subject->setABC('abc');
        self::$subject->setData('efg', 'efg');
        self::$subject->set123('123');
        self::$subject->setData('345', '345');
        self::$subject->setKeyAFirst('value_a_first');
        self::$subject->setData('key_a_2nd', 'value_a_2nd');
        self::$subject->setKeyA3rd('value_a_3rd');
        self::$subject->setData('left', 'over');
        self::assertFalse(self::$subject->isEmpty());

        self::assertSame('abc', self::$subject->getData('a_b_c'));
        self::assertSame('abc', self::$subject->getABC());
        self::$subject->unsetData('a_b_c');

        self::assertSame('efg', self::$subject->getData('efg'));
        self::assertSame('efg', self::$subject->getEfg());
        self::$subject->unsEfg();

        self::assertSame('123', self::$subject->getData('123'));
        self::assertSame('123', self::$subject->get123());
        self::$subject->uns123();

        self::$subject->unsetData('345');

        self::assertSame('value_a_first', self::$subject->getData('key_a_first'));
        self::assertSame('value_a_first', self::$subject->getKeyAFirst());
        self::$subject->unsetData('key_a_first');

        self::assertSame('value_a_2nd', self::$subject->getData('key_a_2nd'));
        self::assertSame('value_a_2nd', self::$subject->getKeyA_2nd());
        self::$subject->unsetData('key_a_2nd');

        self::assertSame('value_a_3rd', self::$subject->getData('key_a3rd'));
        self::assertSame('value_a_3rd', self::$subject->getKeyA3rd());
        self::$subject->unsetData('key_a3rd');

        self::assertSame(['left' => 'over'], self::$subject->getData());

        self::$subject->unsetData();
        self::assertSame([], self::$subject->getData());
        self::assertTrue(self::$subject->isEmpty());

        try {
            /** @phpstan-ignore-next-line */
            self::$subject->notData();
            self::fail('Invalid __call');
        } catch (Varien_Exception $varienException) {
            self::assertStringStartsWith('Invalid method', $varienException->getMessage());
        }
    }

    /**
     * @group Varien_Object
     */
    public function testOffset(): void
    {
        self::assertFalse(self::$subject->offsetExists('off'));

        self::$subject->offsetSet('off', 'set');
        self::assertTrue(self::$subject->offsetExists('off'));
        self::assertSame('set', self::$subject->offsetGet('off'));
        self::assertNull(self::$subject->offsetGet('not-exists'));

        self::$subject->offsetUnset('off');
        self::assertFalse(self::$subject->offsetExists('off'));
    }

    protected function tearDown(): void
    {
        self::$subject->unsetData();
    }
}
