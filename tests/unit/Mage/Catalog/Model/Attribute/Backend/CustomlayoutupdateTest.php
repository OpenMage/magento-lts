<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Attribute\Backend;

use Mage;
use Mage_Catalog_Model_Attribute_Backend_Customlayoutupdate as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class CustomlayoutupdateTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/attribute_backend_customlayoutupdate');
    }
}
