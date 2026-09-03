<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Rss\Model;

use Mage;
use Mage_Rss_Model_Rss as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Rss\Model\RssTrait;

final class RssTest extends OpenMageTest
{
    use RssTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('rss/rss');
    }

    /**
     * Zend_Feed writes the entry title and description into a CDATA section, which does
     * not encode anything, so escaping done by the feed blocks is applied exactly once
     * and must not be repeated here.
     *
     * @group Model
     */
    public function testEntriesAreWrappedInCdataWithoutFurtherEncoding(): void
    {
        self::$subject->_addHeader([
            'title'       => 'Pending product review(s)',
            'description' => 'Pending product review(s)',
            'link'        => 'http://example.org/rss/catalog/review',
            'charset'     => 'UTF-8',
        ]);
        self::$subject->_addEntry([
            'title'       => 'Product: &quot;Foo&quot; review By: &lt;script&gt;alert(1)&lt;/script&gt;',
            'link'        => 'http://example.org/rss/catalog/review',
            'description' => '<p>Review: &lt;script&gt;alert(1)&lt;/script&gt; <br/></p>',
        ]);

        $xml = self::$subject->createRssXml();

        self::assertStringContainsString(
            '<![CDATA[Product: &quot;Foo&quot; review By: &lt;script&gt;alert(1)&lt;/script&gt;]]>',
            $xml,
        );
        self::assertStringNotContainsString('&amp;lt;', $xml);
        self::assertStringNotContainsString('<script>', $xml);
    }
}
