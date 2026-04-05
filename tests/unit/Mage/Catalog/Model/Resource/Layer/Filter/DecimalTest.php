<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Resource\Layer\Filter;

use Mage;
use Mage_Catalog_Model_Resource_Layer_Filter_Decimal as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class DecimalTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/resource_layer_filter_decimal');
    }
}
