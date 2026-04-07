<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogInventory\Model\Resource\Indexer\Stock;

use Mage_CatalogInventory_Model_Resource_Indexer_Stock_Configurable as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogInventory\Model\Resource\Indexer\Stock\ConfigurableTrait;

final class ConfigurableTest extends OpenMageTest
{
    use ConfigurableTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
