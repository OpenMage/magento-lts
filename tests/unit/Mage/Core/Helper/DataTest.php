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

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Data;
use Mage_Core_Model_Encryption;
use PHPUnit\Framework\TestCase;
use Varien_Crypt_Mcrypt;

class DataTest extends TestCase
{
    public const TEST_STRING = '1234567890';

    public Mage_Core_Helper_Data $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/data');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetEncryptor(): void
    {
        $this->assertInstanceOf(Mage_Core_Model_Encryption::class, $this->subject->getEncryptor());
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testEncrypt(): void
    {
        $this->assertIsString($this->subject->encrypt('test'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testDecrypt(): void
    {
        $this->assertIsString($this->subject->decrypt('test'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testValidateKey(): void
    {
        $this->assertInstanceOf(Varien_Crypt_Mcrypt::class, $this->subject->validateKey('test'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetRandomString(): void
    {
        $this->assertIsString($this->subject->getRandomString(5));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetHash(): void
    {
        $this->assertIsString($this->subject->getHash('test'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetHashPassword(): void
    {
        $this->assertIsString($this->subject->getHashPassword('test', 1));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testValidateHash(): void
    {
        $this->assertIsBool($this->subject->validateHash('test', '1'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetStoreId(): void
    {
        $this->assertIsInt($this->subject->getStoreId());
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testRemoveAccents(): void
    {
        $str = 'Ae-Ä Oe-Ö Ue-Ü ae-ä oe-ö ue-ü';
        $this->assertSame('Ae-Ae Oe-Oe Ue-Ue ae-ae oe-oe ue-ue', $this->subject->removeAccents($str, true));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testIsDevAllowed(): void
    {
        $this->assertIsBool($this->subject->isDevAllowed());
    }
}
