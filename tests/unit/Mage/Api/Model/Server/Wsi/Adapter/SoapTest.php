<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api\Model\Server\Wsi\Adapter;

// use Mage;
// use Mage_Api_Model_Server_Wsi_Adapter_Soap as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api\Model\Server\Wsi\Adapter\SoapTrait;

final class SoapTest extends OpenMageTest
{
    use SoapTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('api/server_wsi_adapter_soap');
        self::markTestSkipped('');
    }
}
