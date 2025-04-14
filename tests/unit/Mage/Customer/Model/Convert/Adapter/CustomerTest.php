<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model\Convert\Adapter;

use Mage;
use Mage_Customer_Model_Convert_Adapter_Customer as Subject;
use PHPUnit\Framework\TestCase;
use Throwable;

class CustomerTest extends TestCase
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = Mage::getModel('customer/convert_adapter_customer');
    }

    //    /**
    //     * @return void
    //     * @throws Throwable
    //     *
    //     * @group Mage_Customer
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
    //     * @group Mage_Customer
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
    //     * @group Mage_Customer
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
    //     * @group Mage_Customer
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
    //     * @group Mage_Customer
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
     * @group Mage_Customer
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
