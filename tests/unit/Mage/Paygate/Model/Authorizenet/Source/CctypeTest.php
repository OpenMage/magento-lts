<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paygate\Model\Authorizenet\Source;

use Mage;
use Mage_Paygate_Model_Authorizenet_Source_Cctype as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class CctypeTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('paygate/authorizenet_source_cctype');
    }
}
