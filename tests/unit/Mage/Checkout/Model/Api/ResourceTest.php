<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Checkout\Model\Api;

// use Mage;
// use Mage_Checkout_Model_Api_Resource as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Checkout\Model\Api\ResourceTrait;

final class ResourceTest extends OpenMageTest
{
    use ResourceTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('checkout/api_resource');
        self::markTestSkipped('');
    }
}
