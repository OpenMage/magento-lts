<?php
/**
 * Adminhtml newsletter subscribers grid checkbox item renderer
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid_Renderer_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($row->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
            return '<input type="checkbox" name="subscriber[]" value="' . $row->getId() . '" class="subscriberCheckbox"/>';
        } else {
            return '';
        }
    }
}
