<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console;

use Override;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Varien_Simplexml_Config;

/**
 * Loads the console commands declared under global/console/commands in module config.xml files
 */
final class CommandLoader implements CommandLoaderInterface
{
    public const XML_PATH_COMMANDS = 'global/console/commands';

    /**
     * @var array<string, class-string<Command>>
     */
    private array $commands = [];

    /**
     * @var array<string, string> Declaration key by command name
     */
    private array $keys = [];

    /**
     * @var list<string>
     */
    private array $errors = [];

    /**
     * @param array<string, mixed> $declarations Declarations by key: ['name' => ..., 'class' => ..., 'disabled' => ...]
     */
    public function __construct(array $declarations)
    {
        foreach ($declarations as $key => $declaration) {
            $this->declare((string) $key, is_array($declaration) ? $declaration : []);
        }
    }

    public static function fromConfig(Varien_Simplexml_Config $config): self
    {
        $node = $config->getNode(self::XML_PATH_COMMANDS);
        if ($node === false) {
            return new self([]);
        }

        $declarations = [];
        foreach ($node->children() as $key => $child) {
            $declarations[$key] = $child->asArray();
        }

        return new self($declarations);
    }

    #[Override]
    public function get(string $name): Command
    {
        if (!isset($this->commands[$name])) {
            throw new CommandNotFoundException(sprintf('Command "%s" does not exist.', $name));
        }

        $command = new $this->commands[$name]();
        $command->setName($name);

        return $command;
    }

    #[Override]
    public function has(string $name): bool
    {
        return isset($this->commands[$name]);
    }

    /**
     * @return list<string>
     */
    #[Override]
    public function getNames(): array
    {
        return array_keys($this->commands);
    }

    /**
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array<mixed> $declaration
     */
    private function declare(string $key, array $declaration): void
    {
        if (in_array($declaration['disabled'] ?? '', ['1', 'true'], true)) {
            return;
        }

        $name = is_string($declaration['name'] ?? null) ? trim($declaration['name']) : '';
        $class = is_string($declaration['class'] ?? null) ? ltrim(trim($declaration['class']), '\\') : '';
        if ($name === '' || $class === '') {
            $this->errors[] = sprintf('Console command "%s" is skipped: it needs a <name> and a <class>.', $key);
            return;
        }

        if (!class_exists($class) || !is_subclass_of($class, Command::class)) {
            $this->errors[] = sprintf(
                'Console command "%s" is skipped: class %s does not exist or does not extend %s.',
                $key,
                $class,
                Command::class,
            );
            return;
        }

        if (isset($this->keys[$name])) {
            $this->errors[] = sprintf(
                'Console command "%s" replaces "%s": both declare the name "%s".',
                $key,
                $this->keys[$name],
                $name,
            );
        }

        $this->commands[$name] = $class;
        $this->keys[$name] = $key;
    }
}
