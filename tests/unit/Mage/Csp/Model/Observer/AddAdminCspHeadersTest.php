<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Csp\Model\Observer;

use Mage;
use Mage_Csp_Model_Observer_AddAdminCspHeaders as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Csp\Model\Observer\AddAdminCspHeadersTrait;

final class AddAdminCspHeadersTest extends OpenMageTest
{
    use AddAdminCspHeadersTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('csp/observer_addadmincspheaders');
    }
}
