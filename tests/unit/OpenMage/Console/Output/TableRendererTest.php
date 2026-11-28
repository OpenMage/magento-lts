<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Output;

use InvalidArgumentException;
use OpenMage\Console\Output\TableRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

final class TableRendererTest extends TestCase
{
    private const HEADERS = ['name', 'value'];

    private const ROWS = [['Version', '20.16.0'], ['Note', 'a, "quoted" value'], ['Empty', null]];

    public function testRendersATable(): void
    {
        $display = $this->render('table');

        self::assertMatchesRegularExpression('/\|\s*name\s*\|\s*value\s*\|/', $display);
        self::assertMatchesRegularExpression('/\|\s*Version\s*\|\s*20\.16\.0\s*\|/', $display);
    }

    public function testRendersJsonObjectsKeyedByHeader(): void
    {
        self::assertSame(
            [
                ['name' => 'Version', 'value' => '20.16.0'],
                ['name' => 'Note', 'value' => 'a, "quoted" value'],
                ['name' => 'Empty', 'value' => null],
            ],
            json_decode($this->render('json'), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testRendersCsvWithHeaderLine(): void
    {
        self::assertSame(
            "name,value\nVersion,20.16.0\nNote,\"a, \"\"quoted\"\" value\"\nEmpty,\n",
            $this->render('csv'),
        );
    }

    public function testRendersAnEmptyListAsEmptyJsonArrayAndEmptyCsv(): void
    {
        $output = new BufferedOutput();
        $renderer = new TableRenderer();

        $renderer->render($output, self::HEADERS, [], 'json');
        self::assertSame("[]\n", $output->fetch());

        $renderer->render($output, self::HEADERS, [], 'csv');
        self::assertSame('', $output->fetch());
    }

    public function testRejectsAnUnknownFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('xml');

        TableRenderer::checkFormat('xml');
    }

    private function render(string $format): string
    {
        $output = new BufferedOutput();
        (new TableRenderer())->render($output, self::HEADERS, self::ROWS, $format);

        return $output->fetch();
    }
}
