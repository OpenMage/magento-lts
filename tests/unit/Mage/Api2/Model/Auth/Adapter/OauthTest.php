<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api2\Model\Auth\Adapter;

// use Mage;
// use Mage_Api2_Model_Auth_Adapter_Oauth as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api2\Model\Auth\Adapter\OauthTrait;

final class OauthTest extends OpenMageTest
{
    use OauthTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('api2/auth_adapter_oauth');
        self::markTestSkipped('');
    }
}
