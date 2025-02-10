<?php
/**
 * Mass-action block for process/list grid
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Index
 */
class Mage_Index_Block_Adminhtml_Process_Grid_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
{
    /**
     * Get ids for only visible indexers
     *
     * @return string
     */
    public function getGridIdsJson()
    {
        if (!$this->getUseSelectAll()) {
            return '';
        }

        $ids = [];
        foreach ($this->getParentBlock()->getCollection() as $process) {
            $ids[] = $process->getId();
        }

        return implode(',', $ids);
    }
}
