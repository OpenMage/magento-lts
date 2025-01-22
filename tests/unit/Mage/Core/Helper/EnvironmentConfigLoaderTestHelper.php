<?php

/**
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
