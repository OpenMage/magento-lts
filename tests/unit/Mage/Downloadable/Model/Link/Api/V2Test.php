<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Model\Link\Api;

use Mage;
use Mage_Downloadable_Model_Link_Api_V2 as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class V2Test extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('downloadable/link_api_v2');
    }
}
