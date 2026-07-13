<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Custom import CSV file field for shipping table rates
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Import extends Varien_Data_Form_Element_Abstract
{
    /**
     * @param array $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->setType('file');
    }

    /**
     * @return string
     */
    #[Override]
    public function getElementHtml()
    {
        $html = '';

        $html .= '<input id="time_condition" type="hidden" name="' . $this->getName() . '" value="' . Mage::helper('core/clock')->getTimestamp() . '" />';

        $html .= <<<EndHTML
        <script type="text/javascript">
        document.getElementById('carriers_tablerate_condition_name').addEventListener('change', checkConditionName);
        function checkConditionName(event)
        {
            var conditionNameElement = event.target;
            if (conditionNameElement && conditionNameElement.id) {
                document.getElementById('time_condition').value = '_' + conditionNameElement.value + '/' + Math.random();
            }
        }
        </script>
EndHTML;

        return $html . parent::getElementHtml();
    }
}
