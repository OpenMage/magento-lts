<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Generator;
use Mage;
use Mage_Core_Exception;
use Mage_Core_Model_App;
use Mage_Core_Model_Store;
use Mage_Core_Model_Store_Exception;
use Mage_Core_Model_Store_Group;
use Mage_Core_Model_Website;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public Mage_Core_Model_App $subject;

    public function setUp(): void
    {
        $this->subject = Mage::app();
    }

    /**
     * @dataProvider provideGetStore
     * @group Mage_Core
     * @group Mage_Core_Model
     *
     * @param bool|int|Mage_Core_Model_Store|null|string $id
     */
    public function testGetStore($id): void
    {
        try {
            $this->assertInstanceOf(Mage_Core_Model_Store::class, $this->subject->getStore($id));
        } catch (Mage_Core_Model_Store_Exception $e) {
            $this->assertNotEmpty($e->getMessage());
            $this->assertSame('Invalid store code requested.', $e->getMessage());
        }
    }

    public function provideGetStore(): Generator
    {
        yield 'null' => [
            null,
        ];
        yield 'true' => [
            true,
        ];
        yield 'false' => [
            false,
        ];
        yield 'int valid' => [
            1,
        ];
        yield 'int invalid (exception)' => [
            999,
        ];
        yield 'string' => [
            '1',
        ];
        yield 'Mage_Core_Model_Store' => [
            // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
            new Mage_Core_Model_Store(),
        ];
    }

    /**
     * @dataProvider provideGetWebsite
     * @group Mage_Core
     * @group Mage_Core_Model
     *
     * @param int|Mage_Core_Model_Website|null|string|true $id
     */
    public function testGetWebsite($id): void
    {
        try {
            $this->assertInstanceOf(Mage_Core_Model_Website::class, $this->subject->getWebsite($id));
        } catch (Mage_Core_Exception $e) {
            $this->assertNotEmpty($e->getMessage());
            $this->assertSame('Invalid website id requested.', $e->getMessage());
        }
    }

    public function provideGetWebsite(): Generator
    {
        yield 'null' => [
            null,
        ];
        yield 'true' => [
            true,
        ];
        yield 'false' => [
            false,
        ];
        yield 'int valid' => [
            1,
        ];
        yield 'int invalid (exception)' => [
            999,
        ];
        yield 'string' => [
            '1',
        ];
        yield 'Mage_Core_Model_Website' => [
            // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
            new Mage_Core_Model_Website(),
        ];
    }

    /**
     * @dataProvider provideGetGroup
     * @group Mage_Core
     * @group Mage_Core_Model
     *
     * @param int|Mage_Core_Model_Store_Group|null|string $id
     */
    public function testGetGroup($id): void
    {
        try {
            $this->assertInstanceOf(Mage_Core_Model_Store_Group::class, $this->subject->getGroup($id));
        } catch (Mage_Core_Exception $e) {
            $this->assertNotEmpty($e->getMessage());
            $this->assertSame('Invalid store group id requested.', $e->getMessage());
        }
    }

    public function provideGetGroup(): Generator
    {
        yield 'null' => [
            null,
        ];
        yield 'true' => [
            true,
        ];
        yield 'false' => [
            false,
        ];
        yield 'int valid' => [
            1,
        ];
        yield 'int invalid (exception)' => [
            999,
        ];
        yield 'string' => [
            '1',
        ];
        yield 'Mage_Core_Model_Store_Group' => [
            // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
            new Mage_Core_Model_Store_Group(),
        ];
    }
}
