<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model;

use Generator;
use Mage_Adminhtml_Block_Api_Buttons;
use Mage_Adminhtml_Block_Catalog_Category_Helper_Pricestep;
use Mage_Cms_Block_Block;

trait LayoutTrait
{
    public static function provideCreateBlock(): Generator
    {
        yield 'instance of Mage_Core_Block_Abstract' => [
            Mage_Cms_Block_Block::class,
            true,
            'cms/block',
            null,
            [],
        ];
        yield 'not instance of Mage_Core_Block_Abstract' => [
            false,
            false,
            'rule/conditions',
            null,
            [],
        ];
    }

    public static function provideGetBlockSingleton(): Generator
    {
        $notInstanceOfMageCoreBlockAbstract = $this->getBlockClassesNotInstanceOfMageCoreBlockAbstract();

        $ignoredClasses = array_merge(
            $this->getAbstractBlockClasses(),
            $this->getBlockClassesToMock(),
            $this->getBlockClassesWithErrors(),
            $this->getBlockClassesWithSessions(),
        );

        #$allBlocks = $this->getAllBlockClasses();
        $allBlocks = [
            'adminhtml/api_buttons' => Mage_Adminhtml_Block_Api_Buttons::class,
            'adminhtml/catalog_category_helper_pricestep' => Mage_Adminhtml_Block_Catalog_Category_Helper_Pricestep::class,
        ];

        foreach ($allBlocks as $alias => $className) {
            if (!in_array($className, $ignoredClasses)) {
                yield $className => [
                    $className,
                    !in_array($className, $notInstanceOfMageCoreBlockAbstract),
                    $alias,
                ];
            }
        }
    }
}
