<?php

namespace OpenMage\Dev\Mono;

use Exception;
use Mage_Core_Model_Config;
use Symfony\Component\Filesystem\Filesystem;

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('PS') || define('PS', PATH_SEPARATOR);

class CopyToMonoRepos
{
    public const MODMAN_FILE = 'modman';
    public const TYPE_SOURCE = 'source';
    public const TYPE_TARGET = 'target';

    private Filesystem $filesystem;
    private string $modmanFile;
    private ?string $copyTarget;

    public function __construct(string $pathToModmanFile, ?string $copyTarget)
    {
        $this->copyTarget = $copyTarget;
        $this->filesystem = new Filesystem();
        $this->modmanFile = $pathToModmanFile;
    }

    public static function process(): void
    {
        foreach (self::getModules() as $module) {
            $modman = new CopyToMonoRepos(
                sprintf('.localdev/%s', $module),
                sprintf('.localdev/%s/src', $module),
            );
            $modman->copyMappedFiles();
        }
    }

    public function copyMappedFiles()
    {
        $targets = $this->getModmanMapping(self::TYPE_TARGET);
        foreach ($targets as $target) {
            if ($this->filesystem->exists($target)) {
                if (is_dir($target)) {
                    $this->filesystem->mkdir($this->copyTarget);
                    $this->filesystem->mirror($target, $this->copyTarget . DS . $target, null, ['override' => true]);
                } else {
                    $this->filesystem->copy($target, $this->copyTarget . DS . $target, true);
                }
            }
        }
    }

    /**
     * @param self::TYPE_*|null $type
     */
    public function getModmanMapping(?string $type = null): array
    {
        $mapped  = [];

        try {
            $content = $this->getModmanFileContent();
            $parts = preg_split('/\s+/', $content);


            foreach ($parts as $index => $path) {
                if (!$path) {
                    continue;
                }
                if ($index % 2 == 0) {
                    $mapped[self::TYPE_SOURCE][] = $path;
                } else {
                    $mapped[self::TYPE_TARGET][] = $path;
                }
            }

            if (!is_null($type)) {
                return $mapped[$type];
            }
        } catch (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }

        return $mapped;
    }

    public function getModmanFilePath(): string
    {
        return getcwd() . DS . $this->modmanFile . DS . self::MODMAN_FILE;
    }

    /**
     * @throws Exception
     */
    public function getModmanFileContent(): string
    {
        $file = $this->getModmanFilePath();
        if ($this->filesystem->exists($file)) {
            $content = file_get_contents($file);
            if (!$content) {
                return '';
            }
            return $content;
        }
        throw new Exception(sprintf('File %s not found.', $file));
    }

    public static function getModules(): array
    {
        $modules = [];
        foreach (array_keys(Mage_Core_Model_Config::MAGE_MODULES) as $module) {
            $string = str_replace('Mage_', 'module', $module);
            $data   = preg_split('/(?=[A-Z])/', $string);
            $string = implode('-', $data);
            $modules[$module] = strtolower($string);
        }
        return $modules;
    }
}
