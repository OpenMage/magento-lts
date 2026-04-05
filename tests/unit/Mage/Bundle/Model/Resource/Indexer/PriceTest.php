<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Bundle\Model\Resource\Indexer;

use Mage;
use Mage_Bundle_Model_Resource_Indexer_Price as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class PriceTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('bundle/resource_indexer_price');
    }
}
