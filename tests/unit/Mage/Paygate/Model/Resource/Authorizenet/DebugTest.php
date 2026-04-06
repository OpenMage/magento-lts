<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paygate\Model\Resource\Authorizenet;

use Mage;
use Mage_Paygate_Model_Resource_Authorizenet_Debug as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paygate\Model\Resource\Authorizenet\DebugTrait;

final class DebugTest extends OpenMageTest
{
    use DebugTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('paygate/resource_authorizenet_debug');
    }
}
