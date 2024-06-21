<?php

/**
 * Custom Text Form Element for OAuth2
 */
class Mage_Oauth2_Block_Adminhtml_Text extends Varien_Data_Form_Element_Text
{
    /**
     * Get HTML attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();
        $attributes[] = 'data-copy-text';
        return $attributes;
    }
}
