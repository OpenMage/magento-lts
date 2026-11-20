<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien;

use OpenMage\Tests\Unit\OpenMageTest;
use Override;
use Varien_Autoload as Subject;

final class AutoloadTest extends OpenMageTest
{
    private const FIXTURES = [
        'Fixture.php' => "<?php\n\nclass OpenMageAutoloadTest_Fixture\n{\n}\n",
        // Optional parameter before a required one raises E_DEPRECATED while the file is compiled
        'Deprecated.php' => "<?php\n\nclass OpenMageAutoloadTest_Deprecated\n{\n    public function run(\$a = null, \$b) {}\n}\n",
        'HandledDeprecated.php' => "<?php\n\nclass OpenMageAutoloadTest_HandledDeprecated\n{\n    public function run(\$a = null, \$b) {}\n}\n",
        'UserNotice.php' => "<?php\n\ntrigger_error('OpenMageAutoloadTest notice', E_USER_NOTICE);\n\nclass OpenMageAutoloadTest_UserNotice\n{\n}\n",
    ];

    private static string $fixtureDir;

    private static string $originalIncludePath;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$fixtureDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('openmage_autoload_', true);
        mkdir(self::$fixtureDir . DIRECTORY_SEPARATOR . 'OpenMageAutoloadTest', 0777, true);
        foreach (self::FIXTURES as $file => $content) {
            file_put_contents(self::$fixtureDir . DIRECTORY_SEPARATOR . 'OpenMageAutoloadTest' . DIRECTORY_SEPARATOR . $file, $content);
        }

        self::$originalIncludePath = get_include_path();
        set_include_path(self::$originalIncludePath . PATH_SEPARATOR . self::$fixtureDir);
    }

    #[Override]
    public static function tearDownAfterClass(): void
    {
        set_include_path(self::$originalIncludePath);

        foreach (array_keys(self::FIXTURES) as $file) {
            unlink(self::$fixtureDir . DIRECTORY_SEPARATOR . 'OpenMageAutoloadTest' . DIRECTORY_SEPARATOR . $file);
        }

        rmdir(self::$fixtureDir . DIRECTORY_SEPARATOR . 'OpenMageAutoloadTest');
        rmdir(self::$fixtureDir);

        parent::tearDownAfterClass();
    }

    /**
     * @group Varien_Autoload
     */
    public function testAutoloadIncludesClassFromIncludePath(): void
    {
        self::assertFalse(class_exists('OpenMageAutoloadTest_Fixture', false));

        Subject::instance()->autoload('OpenMageAutoloadTest_Fixture');

        self::assertTrue(class_exists('OpenMageAutoloadTest_Fixture', false));
    }

    /**
     * Other autoloaders and class_exists() probes rely on a missing class failing silently
     *
     * @group Varien_Autoload
     */
    public function testAutoloadIgnoresMissingClassSilently(): void
    {
        $errors = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$errors): bool {
            $errors[] = $errstr;

            return true;
        });

        try {
            $result = Subject::instance()->autoload('OpenMageAutoloadTest_Missing');
            $exists = class_exists('OpenMageAutoloadTest_Missing');
        } finally {
            restore_error_handler();
        }

        self::assertNull($result);
        self::assertFalse($exists);
        self::assertSame([], $errors);
    }

    /**
     * Errors raised by an included class file must still reach the active error handler, unsuppressed
     *
     * @group Varien_Autoload
     */
    public function testAutoloadForwardsErrorsOfIncludedFile(): void
    {
        $errors = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$errors): bool {
            // Handlers are also called for errors silenced with @, like mageCoreErrorHandler() those are ignored
            if ((error_reporting() & $errno) !== 0) {
                $errors[] = [$errno, $errstr];
            }

            return true;
        });

        try {
            Subject::instance()->autoload('OpenMageAutoloadTest_Deprecated');
        } finally {
            restore_error_handler();
        }

        self::assertTrue(class_exists('OpenMageAutoloadTest_Deprecated', false));
        self::assertCount(1, $errors);
        self::assertSame(E_DEPRECATED, $errors[0][0]);
        self::assertStringContainsString('Optional parameter $a declared before required parameter $b', $errors[0][1]);
    }

    /**
     * A handler that returns null, like mageCoreErrorHandler(), has handled the error: PHP's own handler must not run
     *
     * @group Varien_Autoload
     */
    public function testAutoloadKeepsNullReturnOfErrorHandler(): void
    {
        $errors = [];
        // PHP accepts any return value, only false lets its own handler run; the stub only allows bool
        /** @phpstan-ignore argument.type */
        set_error_handler(static function (int $errno, string $errstr) use (&$errors): ?bool {
            if ((error_reporting() & $errno) !== 0) {
                $errors[] = $errstr;
            }

            return null;
        });

        error_clear_last();
        try {
            Subject::instance()->autoload('OpenMageAutoloadTest_HandledDeprecated');
        } finally {
            restore_error_handler();
        }

        self::assertTrue(class_exists('OpenMageAutoloadTest_HandledDeprecated', false));
        self::assertCount(1, $errors);
        // error_get_last() is only set by PHP's own handler
        self::assertNull(error_get_last());
    }

    /**
     * An error handler registered for some levels only must not receive other levels raised by an included file
     *
     * @group Varien_Autoload
     */
    public function testAutoloadKeepsErrorLevelsOfErrorHandler(): void
    {
        $errors = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$errors): bool {
            $errors[] = [$errno, $errstr];

            return true;
        }, E_USER_WARNING);
        // The E_USER_NOTICE goes to PHP's own handler; keep that one quiet
        $errorReporting = error_reporting(E_ALL & ~E_USER_NOTICE);

        try {
            Subject::instance()->autoload('OpenMageAutoloadTest_UserNotice');
        } finally {
            error_reporting($errorReporting);
            restore_error_handler();
        }

        self::assertTrue(class_exists('OpenMageAutoloadTest_UserNotice', false));
        self::assertSame([], $errors);
    }
}
