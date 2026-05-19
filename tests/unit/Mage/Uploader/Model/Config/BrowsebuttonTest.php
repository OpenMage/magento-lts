<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Model\Config;

// use Mage;
// use Mage_Uploader_Model_Config_Browsebutton as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Uploader\Model\Config\BrowsebuttonTrait;

final class BrowsebuttonTest extends OpenMageTest
{
    use BrowsebuttonTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('uploader/config_browsebutton');
        self::markTestSkipped('');
    }
}
