<?php
/**
 * Adminhtml grid widget massaction item additional action default
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Massaction_Item_Additional_Default extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Grid_Massaction_Item_Additional_Interface
{
    /**
     * @return $this
     */
    public function createFromConfiguration(array $configuration)
    {
        $form = new Varien_Data_Form();

        foreach ($configuration as $itemId => $item) {
            $item['class'] = isset($item['class']) ? $item['class'] . ' absolute-advice' : 'absolute-advice';
            $form->addField($itemId, $item['type'], $item);
        }
        $this->setForm($form);
        return $this;
    }
}
