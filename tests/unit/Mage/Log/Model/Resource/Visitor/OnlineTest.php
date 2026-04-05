<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Log\Model\Resource\Visitor;

use Mage;
use Mage_Log_Model_Resource_Visitor_Online as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class OnlineTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('log/resource_visitor_online');
    }
}
