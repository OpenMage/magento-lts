<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\System\Store\Delete\Form;

use Magento\Mtf\Block\Form;

/**
 * Form for website deleted.
 */
class GroupForm extends Form
{
    /**
     * Fill delete form.
     *
     * @return void
     * @throws \Exception
     */
    public function fillForm()
    {
        $mapping = $this->dataMapping(['create_backup' => 'No']);
        $this->_fill($mapping);
    }
}
