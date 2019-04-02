<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Mtf\Util\Generate;

/**
 * Class Page.
 *
 * Page classes generator.
 *
 * @internal
 */
class Page extends AbstractGenerate
{
    /**
     * @var \Magento\Mtf\Config\DataInterface
     */
    protected $configData;

    /**
     * Launch generation of all page classes.
     *
     * @return void
     */
    public function launch()
    {
        $this->cnt = 0;

        foreach ($this->configData->get('page') as $name => $data) {
            $this->generateClass($name, $data);
        }

        \Magento\Mtf\Util\Generate\GenerateResult::addResult('Page Classes', $this->cnt);
    }

    /**
     * Generate single page class.
     *
     * @param string $className
     * @return string|bool
     * @throws \InvalidArgumentException
     */
    public function generate($className)
    {
        $classNameParts = explode('\\', $className);
        $classDataKey = 'page/' . end($classNameParts);

        if (!$this->configData->get($classDataKey)) {
            throw new \InvalidArgumentException('Invalid class name: ' . $className);
        }

        return $this->generateClass(
            end($classNameParts),
            $this->configData->get($classDataKey)
        );
    }

    /**
     * Generate page class from XML source.
     *
     * @param string $name
     * @param array $data
     * @return string|bool
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function generateClass($name, array $data)
    {
        $className = ucfirst($name);
        $module = str_replace('_', '/', $data['module']);
        $area = empty($data['area']) ? null : $data['area'];
        $folderPath = $module . '/Test/Page' . (($area === null) ? '' : ('/' . $area));
        $realFolderPath = MTF_BP . '/generated/' . $folderPath;
        $namespace = str_replace('/', '\\', $folderPath);
        $mca = isset($data['mca']) ? $data['mca'] : '';
        $areaMtfPage = $this->getParentPage($folderPath, $mca, $area);
        $blocks = isset($data['block']) ? $data['block'] : [];

        $content = "<?php\n";
        $content .= $this->getFilePhpDoc();
        $content .= "namespace {$namespace};\n\n";
        $content .= "use Magento\\Mtf\\Page\\{$areaMtfPage};\n\n";
        $content .= "/**\n";
        $content .= " * Class {$className}\n";
        $content .= " */\n";
        $content .= "class {$className} extends {$areaMtfPage}\n";
        $content .= "{\n";
        $content .= "    const MCA = '{$mca}';\n\n";

        $content .= "    /**\n";
        $content .= "     * Blocks' config\n";
        $content .= "     *\n";
        $content .= "     * @var array\n";
        $content .= "     */\n";
        $content .= "    protected \$blocks = [\n";
        foreach ($blocks as $blockName => $block) {
            $content .= $this->generatePageClassBlock($blockName, $block, '        ');
        }
        $content .= "    ];\n";

        foreach ($blocks as $blockName => $block) {
            $content .= "\n    /**\n";
            $content .= "     * @return \\{$block['class']}\n";
            $content .= "     */\n";
            $content .= '    public function get' . ucfirst($blockName) . '()' . "\n";
            $content .= "    {\n";
            $content .= "        return \$this->getBlockInstance('{$blockName}');\n";
            $content .= "    }\n";
        }

        $content .= "}\n";

        $newFilename = $className . '.php';

        if (file_exists($realFolderPath . '/' . $newFilename)) {
            unlink($realFolderPath . '/' . $newFilename);
        }

        if (!is_dir($realFolderPath)) {
            mkdir($realFolderPath, 0777, true);
        }

        $result = @file_put_contents($realFolderPath . '/' . $newFilename, $content);

        if ($result === false) {
            $error = error_get_last();
            $this->addError(sprintf('Unable to generate %s class. Error: %s', $className, $error['message']));
            return false;
        }

        $this->cnt++;

        return $realFolderPath . '/' . $newFilename;
    }

    /**
     * Generate block for page class.
     *
     * @param string $blockName
     * @param array $params
     * @param string $indent
     * @return string
     */
    protected function generatePageClassBlock($blockName, array $params, $indent = '')
    {
        $content = $indent . "'{$blockName}' => [\n";
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $content .= $this->generatePageClassBlock($key, $value, $indent . '    ');
            } else {
                $escaped = str_replace('\'', '"', $value);
                $content .= $indent . "    '{$key}' => '{$escaped}',\n";
            }
        }
        $content .= $indent . "],\n";

        return $content;
    }

    /**
     * Determine parent page class.
     *
     * @param string $folderPath
     * @param string $mca
     * @param string|null $area
     * @return string
     */
    protected function getParentPage($folderPath, $mca, $area)
    {
        if (strpos($folderPath, 'Adminhtml') !== false && $area === 'Adminhtml') {
            $areaMtfPage = 'BackendPage';
        } else {
            if (strpos($mca, 'http') === false) {
                $areaMtfPage = 'FrontendPage';
            } else {
                $areaMtfPage = 'ExternalPage';
            }
        }

        return $areaMtfPage;
    }
}
