<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage_Core_Helper_EnvironmentConfigLoader;

class EnvironmentConfigLoaderTestHelper extends Mage_Core_Helper_EnvironmentConfigLoader
{
    public function exposedBuildPath(string $section, string $group, string $field): string
    {
        return $this->buildPath($section, $group, $field);
    }

    public function exposedBuildNodePath(string $scope, string $path): string
    {
        return $this->buildNodePath($scope, $path);
    }
}
