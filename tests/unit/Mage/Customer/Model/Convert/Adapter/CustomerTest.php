<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model\Convert\Adapter;

use Mage;
use Mage_Customer_Model_Convert_Adapter_Customer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Throwable;

final class CustomerTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('customer/convert_adapter_customer');
    }

    //    /**
    //     * @return void
    //     * @throws Throwable
    //     *
    //     * @group Model
    //     */
    //    public function testSaveRowNoWebsite(): void
    //    {
    //        $data = [];
    //        try {
    //            self::$subject->saveRow($data);
    //            $this->fail();
    //        } catch (Mage_Core_Exception $e) {
    //            $this->assertSame('Skipping import row, required field "website" is not defined.', $e->getMessage());
    //        }
    //    }
    //
    //    /**
    //     * @return void
    //     * @throws Throwable
    //     *
    //     * @group Model
    //     */
    //    public function testSaveRowNoEmail(): void
    //    {
    //        $data = [
    //            'website'   => 'base',
    //        ];
    //        try {
    //            self::$subject->saveRow($data);
    //            $this->fail();
    //        } catch (Mage_Core_Exception $e) {
    //            $this->assertSame('Skipping import row, required field "email" is not defined.', $e->getMessage());
    //        }
    //    }
    //
    //    /**
    //     * @return void
    //     * @throws Throwable
    //     *
    //     * @group Model
    //     */
    //    public function testSaveRowNoGroup(): void
    //    {
    //        $data = [
    //            'website'   => 'base',
    //            'email'     => 'test@example.com',
    //        ];
    //        try {
    //            self::$subject->saveRow($data);
    //            $this->fail();
    //        } catch (Mage_Core_Exception $e) {
    //            $this->assertSame('Skipping import row, the value "" is not valid for the "group" field.', $e->getMessage());
    //        }
    //    }
    //
    //    /**
    //     * @return void
    //     * @throws Throwable
    //     *
    //     * @group Model
    //     */
    //    public function testSaveRowNoFirstname(): void
    //    {
    //        $data = [
    //            'website'   => 'base',
    //            'email'     => 'test@example.com',
    //            'group'     => 'General',
    //        ];
    //        try {
    //            self::$subject->saveRow($data);
    //            $this->fail();
    //        } catch (Mage_Core_Exception $e) {
    //            $this->expectExceptionMessage('Skip import row, required field "firstname" for the new customer is not defined.');
    //        }
    //    }
    //
    //    /**
    //     * @return void
    //     * @throws Throwable
    //     *
    //     * @group Model
    //     */
    //    public function testSaveRowNoLastname(): void
    //    {
    //        $data = [
    //            'website'   => 'base',
    //            'email'     => 'test@example.com',
    //            'group'     => 'General',
    //            'firstname' => 'John',
    //        ];
    //        try {
    //            self::$subject->saveRow($data);
    //            $this->fail();
    //        } catch (Mage_Core_Exception $e) {
    //            $this->assertSame('Skip import row, required field "lastname" for the new customer is not defined.', $e->getMessage());
    //        }
    //    }
    /**
     * @throws Throwable
     * @group Model
     */
    public function testSaveRow(): void
    {
        $data = [
            'website'   => 'base',
            'email'     => 'test@example.com',
            'group'     => 'General',
            'firstname' => 'John',
            'lastname'  => 'Doe',
        ];
        static::assertInstanceOf(Subject::class, self::$subject->saveRow($data));
    }
}
