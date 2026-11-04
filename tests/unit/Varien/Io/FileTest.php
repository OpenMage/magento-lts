<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Io;

use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Io_File as Subject;

final class FileTest extends OpenMageTest
{
    public function testStreamWriteCsvDelimiter(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'csv');
        self::assertIsString($file);
        $subject = new Subject();
        $subject->open(['path' => dirname($file)]);
        $subject->streamOpen($file, 'w');
        try {
            $subject->streamWriteCsv(['a', 'b']);
            $subject->setCsvDelimiter(';');
            $subject->streamWriteCsv(['a', 'b']);
            $subject->streamWriteCsv(['a', 'b'], ',');
            $subject->streamWriteCsv(['a', 'b'], enclosure: "'");
        } finally {
            $subject->streamClose();
        }

        try {
            self::assertSame("a,b\na;b\na,b\na;b\n", file_get_contents($file));
        } finally {
            unlink($file);
        }
    }
}
