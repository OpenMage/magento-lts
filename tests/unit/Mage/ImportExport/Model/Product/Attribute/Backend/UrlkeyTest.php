<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ImportExport\Model\Product\Attribute\Backend;

# use Mage;
use Mage_ImportExport_Model_Product_Attribute_Backend_Urlkey as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\ImportExport\Model\Product\Attribute\Backend\UrlkeyTrait;

final class UrlkeyTest extends OpenMageTest
{
    use UrlkeyTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('importexport/product_attribute_backend_urlkey');
        self::markTestSkipped('');
    }
}
