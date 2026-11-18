<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Email;

use Mage;
use Mage_Core_Model_Email_Template as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class TemplateVariablesTest extends OpenMageTest
{
    /**
     * Template variable labels come from a posted field and are shown in the "Insert Variable..." dialog.
     *
     * @group Model
     */
    public function testGetVariablesOptionArrayEscapesLabels(): void
    {
        /** @var Subject $template */
        $template = Mage::getModel('core/email_template');
        $template->setData('orig_template_variables', json_encode([
            'var order.increment_id' => 'Order <b>Id</b>',
            "var order.getCreatedAtFormated('long')" => '<img src=x onerror=alert(1)>',
            'var ignored' => ['not', 'a', 'label'],
        ]));

        self::assertSame([
            'label' => 'Template Variables',
            'value' => [
                ['value' => '{{var order.increment_id}}', 'label' => 'Order &lt;b&gt;Id&lt;/b&gt;'],
                ['value' => "{{var order.getCreatedAtFormated('long')}}", 'label' => '&lt;img src=x onerror=alert(1)&gt;'],
            ],
        ], $template->getVariablesOptionArray(true));
    }

    /**
     * @group Model
     */
    public function testGetVariablesOptionArrayIgnoresNonObjectJson(): void
    {
        /** @var Subject $template */
        $template = Mage::getModel('core/email_template');
        $template->setData('orig_template_variables', '"just a string"');

        self::assertSame([], $template->getVariablesOptionArray(true));
    }
}
