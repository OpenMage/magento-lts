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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Compiler
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Compilation process model
 *
 * @category    Mage
 * @package     Mage_Compiler
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Compiler_Model_Process
{
    protected $_compileDir      = null;
    protected $_includeDir      = null;
    protected $_statDir         = null;
    protected $_compileConfig   = null;
    protected $_includePaths    = array();
    protected $_processedClasses= array();

    protected $_controllerFolders = array();

    public function __construct($options=array())
    {
        if (isset($options['compile_dir'])) {
            $this->_compileDir = $options['compile_dir'];
        } else {
            $this->_compileDir = Mage::getBaseDir() . DS . 'includes';
        }
        $this->_includeDir  = $this->_compileDir . DS . 'src';
        $this->_statDir     = $this->_compileDir . DS . 'stat';
    }

    /**
     * Get compilation config
     *
     * @return Mage_Core_Model_Config_Base
     */
    public function getCompileConfig()
    {
        if ($this->_compileConfig === null) {
            $this->_compileConfig   = Mage::getConfig()->loadModulesConfiguration('compilation.xml');
        }
        return $this->_compileConfig;
    }

    /**
     * Get allowed include paths
     *
     * @return array
     */
    protected function _getIncludePaths()
    {
        if (empty($this->_includePaths)) {
            $originalPath = Mage::registry('original_include_path');
            /**
             * Exclude current dirrectory include path
             */
            if ($originalPath == '.') {
                $path = get_include_path();
            } else {
                $path = str_replace($originalPath, '', get_include_path());
            }
            
            $this->_includePaths = explode(PS, $path);
            foreach ($this->_includePaths as $index => $path) {
                if (empty($path) || $path == '.') {
                    unset($this->_includePaths[$index]);
                }
            }
        }
        return $this->_includePaths;
    }

    /**
     * Copy directory
     *
     * @param   string $source
     * @param   string $target
     * @return  Mage_Compiler_Model_Process
     */
    protected function _copy($source, $target, $firstIteration = true)
    {
        if (is_dir($source)) {
            $dir = dir($source);
            while (false !== ($file = $dir->read())) {
                if (($file[0] == '.')) {
                    continue;
                }
                $sourceFile = $source . DS . $file;
                if ($file == 'controllers') {
                    $this->_controllerFolders[] = $sourceFile;
                    continue;
                }

                if ($firstIteration) {
                    $targetFile = $target . DS . $file;
                } else {
                    $targetFile = $target . '_' . $file;
                }
                $this->_copy($sourceFile, $targetFile, false);
            }
        } else {
            if (strpos(str_replace($this->_includeDir, '', $target), '-')
                || !in_array(substr($source, strlen($source)-4, 4), array('.php'))) {
                return $this;
            }
            copy($source, $target);
        }
        return $this;
    }

    /**
     * Copy Zend Locale data to compilation folter
     *
     * @param   string $destDir
     * @return  Mage_Compiler_Model_Process
     */
    protected function _copyZendLocaleData($destDir)
    {
        $source = Mage::getBaseDir('lib').DS.'Zend'.DS.'Locale'.DS.'Data';
        $dir = dir($source);
        while (false !== ($file = $dir->read())) {
            if (($file[0] == '.')) {
                continue;
            }
            $sourceFile = $source . DS . $file;
            $targetFile = $destDir . DS . $file;
            copy($sourceFile, $targetFile);
        }
        return $this;
    }

    /**
     * Copy controllers with folders structure
     *
     * @param   string $basePath base include path where files are located
     * @return  Mage_Compiler_Model_Process
     */
    protected function _copyControllers($basePath)
    {
        foreach ($this->_controllerFolders as $path) {
            $relPath = str_replace($basePath, '', $path);
            $relPath = trim($relPath, DS);
            $arrDirs = explode(DS, $relPath);
            $destPath = $this->_includeDir;
            foreach ($arrDirs as $dir) {
                $destPath.= DS.$dir;
                $this->_mkdir($destPath);
            }
            $this->_copyAll($path, $destPath);
        }
        return $this;
    }

    /**
     * Copy all files and subfolders
     *
     * @param   string $source
     * @param   string $target
     * @return  Mage_Compiler_Model_Process
     */
    protected function _copyAll($source, $target)
    {
        if (is_dir($source)) {
            $this->_mkdir($target);
            $dir = dir($source);
            while (false !== ($file = $dir->read())) {
                if (($file[0] == '.')) {
                    continue;
                }
                $sourceFile = $source . DS . $file;
                $targetFile = $target . DS . $file;
                $this->_copyAll($sourceFile, $targetFile);
            }
        } else {
            if (!in_array(substr($source, strlen($source)-4, 4), array('.php'))) {
                return $this;
            }
            copy($source, $target);
        }
        return $this;
    }

    /**
     * Create directory if not exist
     *
     * @param   string $dir
     * @return  string
     */
    protected function _mkdir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir);
            @chmod($dir, 0777);
        }
        return $dir;
    }

    /**
     * Copy files from all include directories to one.
     * Lib files and controllers files will be copied as is
     *
     * @return Mage_Compiler_Model_Process
     */
    protected function _collectFiles()
    {
        $paths  = $this->_getIncludePaths();
        $paths  = array_reverse($paths);
        $destDir= $this->_includeDir;
        $libDir = Mage::getBaseDir('lib');

        $this->_mkdir($destDir);
        foreach ($paths as $path) {
            $this->_controllerFolders = array();
            $this->_copy($path, $destDir);
            $this->_copyControllers($path);
            if ($path == $libDir) {
                $this->_copyAll($libDir, $destDir);
            }
        }

        $destDir.= DS.'Data';
        $this->_mkdir($destDir);
        $this->_copyZendLocaleData($destDir);
        return $this;
    }

    public function getCollectedFilesCount()
    {
        return count(glob($this->_includeDir.DS.'*'));
    }

    public function getCompiledFilesCount()
    {
        return count(glob($this->_includeDir.DS.Varien_Autoload::SCOPE_FILE_PREFIX.'*'));
    }

    public function getCompileClassList()
    {
        $arrFiles = array();
        $statDir  = $this->_statDir;
        $statFiles= array();
        if (is_dir($statDir)) {
            $dir = dir($statDir);
            while (false !== ($file = $dir->read())) {
                if (($file[0] == '.')) {
                    continue;
                }
                $statFiles[str_replace('.csv', '', $file)] = $this->_statDir.DS.$file;
            }
        }

        foreach ($this->getCompileConfig()->getNode('includes')->children() as $code => $config) {
            $classes = $config->asArray();
            if (is_array($classes)) {
                $arrFiles[$code] = array_keys($classes);
            } else {
                $arrFiles[$code] = array();
            }

            $statClasses = array();
            if (isset($statFiles[$code])) {
                $statClasses = explode("\n", file_get_contents($statFiles[$code]));
                $popularStatClasses = array();
                foreach ($statClasses as $index => $classInfo) {
                    $classInfo = explode(':', $classInfo);
                    $popularStatClasses[$classInfo[1]][] = $classInfo[0];
                }
                ksort($popularStatClasses);
                $statClasses = array_pop($popularStatClasses);
                unset($statFiles[$code]);
            }
            $arrFiles[$code] = array_merge($arrFiles[$code], $statClasses);
            $arrFiles[$code] = array_unique($arrFiles[$code]);
            sort($arrFiles[$code]);
        }

        foreach($statFiles as $code => $file) {
            $classes = explode("\n", file_get_contents($file));
            $popularStatClasses = array();
            foreach ($classes as $index => $classInfo) {
                $classInfo = explode(':', $classInfo);
                $popularStatClasses[$classInfo[1]][] = $classInfo[0];
            }
            ksort($popularStatClasses);
            $arrFiles[$code] = array_pop($popularStatClasses);
        }
        foreach ($arrFiles as $scope=>$classes) {
            if ($scope != 'default') {
                foreach ($classes as $index => $class) {
                    if (in_array($class, $arrFiles['default'])) {
                        unset($arrFiles[$scope][$index]);
                    }
                }
            }
        }
        return $arrFiles;
    }

    /**
     * Compile classes code to files
     *
     * @return Mage_Compiler_Model_Process
     */
    protected function _compileFiles()
    {
        $classesInfo = $this->getCompileClassList();

        foreach ($classesInfo as $code => $classes) {
            $classesSorce = $this->_getClassesSourceCode($classes, $code);
            file_put_contents($this->_includeDir.DS.Varien_Autoload::SCOPE_FILE_PREFIX.$code.'.php', $classesSorce);
        }
        return $this;
    }

    protected function _getClassesSourceCode($classes, $scope)
    {
        $sortedClasses = array();
        foreach ($classes as $className) {
            $implements = array_reverse(class_implements($className));
            foreach ($implements as $class) {
                if (!in_array($class, $sortedClasses) && !in_array($class, $this->_processedClasses) && strstr($class, '_')) {
                    $sortedClasses[] = $class;
                    if ($scope == 'default') {
                        $this->_processedClasses[] = $class;
                    }
                }
            }
            $extends    = array_reverse(class_parents($className));
            foreach ($extends as $class) {
                if (!in_array($class, $sortedClasses) && !in_array($class, $this->_processedClasses) && strstr($class, '_')) {
                    $sortedClasses[] = $class;
                    if ($scope == 'default') {
                        $this->_processedClasses[] = $class;
                    }
                }
            }
            if (!in_array($className, $sortedClasses) && !in_array($className, $this->_processedClasses)) {
                $sortedClasses[] = $className;
                    if ($scope == 'default') {
                        $this->_processedClasses[] = $className;
                    }
            }
        }

        $classesSource = "<?php\n";
        foreach ($sortedClasses as $className) {
            $file = $this->_includeDir.DS.$className.'.php';
            if (!file_exists($file)) {
                continue;
            }
            $content = file_get_contents($file);
            $content = ltrim($content, '<?php');
            $content = rtrim($content, "\n\r\t?>");
            $classesSource.= $content;
        }
        return $classesSource;
    }

    public function clear()
    {
        $this->registerIncludePath(false);
        if (is_dir($this->_includeDir)) {
            mageDelTree($this->_includeDir);
        }
        return $this;
    }

    /**
     * Run compilation process
     *
     * @return Mage_Compiler_Model_Process
     */
    public function run()
    {
        $this->_collectFiles();
        $this->_compileFiles();
        $this->registerIncludePath();
        return $this;

    }

    /**
     * Enable or disable include path constant definition in compiler config.php
     *
     * @param   bool $flag
     * @return  Mage_Compiler_Model_Process
     */
    public function registerIncludePath($flag = true)
    {
        $file = $this->_compileDir.DS.'config.php';
        if (is_writeable($file)) {
            $content = file_get_contents($file);
            $content = explode("\n", $content);
            foreach ($content as $index => $line) {
                if (strpos($line, 'COMPILER_INCLUDE_PATH')) {
                    if ($flag) {
                        $content[$index] = ltrim($line, '#');
                    } else {
                        $content[$index] = '#'.$line;
                    }
                }
            }
            file_put_contents($file, implode("\n", $content));
        }
        return $this;
    }

    /**
     * Validate if compilation process is allowed
     *
     * @return array
     */
    public function validate()
    {
        $result = array();
        if (!is_writeable($this->_compileDir)) {
            $result[] = Mage::helper('compiler')->__('Directory "%s" must be writeable', $this->_compileDir);
        }
        $file = $this->_compileDir.DS.'config.php';
        if (!is_writeable($file)) {
            $result[] = Mage::helper('compiler')->__('File "%s" must be writeable', $file);
        }
        return $result;
    }

}
