<?php

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class Mage_Core_Model_SymfonyKernelCompilerPass implements CompilerPassInterface
{
    private readonly string $codeDir;

    public function __construct(string $codeDir)
    {
        $this->codeDir = $codeDir;
    }

    public function process(ContainerBuilder $container): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->codeDir, FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            /** @var SplFileInfo $file */
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            if ($contents === false || !str_contains($contents, '#[Mage_Core_Model_OpenMageDi]')) {
                continue;
            }

            $className = $this->extractClassName($file->getPathname());

            $definition = new Definition($className);
            $definition->setAutowired(true);
            $definition->setAutoconfigured(true);
            $definition->setPublic(true);
            $container->setDefinition($className, $definition);
        }
    }

    private function extractClassName(string $filePath): ?string
    {
        $contents = file_get_contents($filePath);
        if ($contents === false) {
            return null;
        }

        $tokens = token_get_all($contents);
        $count = count($tokens);
        $namespace = '';

        for ($i = 0; $i < $count; $i++) {
            if (!is_array($tokens[$i])) {
                continue;
            }

            if ($tokens[$i][0] === T_NAMESPACE) {
                $namespaceParts = '';
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j] === ';' || $tokens[$j] === '{') {
                        break;
                    }

                    if (is_array($tokens[$j]) && in_array($tokens[$j][0], [T_STRING, T_NAME_QUALIFIED], true)) {
                        $namespaceParts .= $tokens[$j][1];
                    }
                }

                $namespace = $namespaceParts;
            }

            if ($tokens[$i][0] === T_CLASS) {
                $prev = $i - 1;
                while ($prev >= 0 && is_array($tokens[$prev]) && $tokens[$prev][0] === T_WHITESPACE) {
                    $prev--;
                }

                if ($prev >= 0 && is_array($tokens[$prev]) && $tokens[$prev][0] === T_DOUBLE_COLON) {
                    continue;
                }

                for ($j = $i + 1; $j < $count; $j++) {
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                        $class = $tokens[$j][1];
                        return $namespace !== '' ? $namespace . '\\' . $class : $class;
                    }
                }
            }
        }

        return null;
    }
}
