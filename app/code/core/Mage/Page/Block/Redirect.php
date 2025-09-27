<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Page
 */

/**
 * Customer Redirect Page
 *
 * @package    Mage_Page
 *
 * @method string getMethod()
 */
class Mage_Page_Block_Redirect extends Mage_Core_Block_Template
{
    /**
     *  HTML form hidden fields
     */
    protected $_formFields = [];

    /**
     *  URL for redirect location
     *
     *  @return   string URL
     */
    public function getTargetURL()
    {
        return '';
    }

    /**
     *  Additional custom message
     *
     *  @return   string Output message
     */
    public function getMessage()
    {
        return '';
    }

    /**
     *  Client-side redirect engine output
     *
     *  @return   string
     */
    public function getRedirectOutput()
    {
        if ($this->isHtmlFormRedirect()) {
            return $this->getHtmlFormRedirect();
        } else {
            return $this->getJsRedirect();
        }
    }

    /**
     *  Redirect via JS location
     *
     *  @return   string
     */
    public function getJsRedirect()
    {
        $js  = '<script type="text/javascript">';
        $js .= 'document.location.href="' . $this->getTargetURL() . '";';
        return $js . '</script>';
    }

    /**
     *  Redirect via HTML form submission
     *
     *  @return   string
     */
    public function getHtmlFormRedirect()
    {
        $form = new Varien_Data_Form();
        $form->setAction($this->getTargetURL())
            ->setId($this->getFormId())
            ->setName($this->getFormId())
            ->setMethod($this->getMethod())
            ->setUseContainer(true);
        foreach ($this->_getFormFields() as $field => $value) {
            $form->addField($field, 'hidden', ['name' => $field, 'value' => $value]);
        }
        $html = $form->toHtml();
        return $html . ('<script type="text/javascript">document.getElementById("' . $this->getFormId() . '").submit();</script>');
    }

    /**
     * HTML form or JS redirect
     *
     * @return bool
     */
    public function isHtmlFormRedirect()
    {
        return is_array($this->_getFormFields()) && count($this->_getFormFields()) > 0;
    }

    /**
     *  HTML form id/name attributes
     *
     *  @return   string Id/name
     */
    public function getFormId()
    {
        return '';
    }

    /**
     *  HTML form method attribute
     *
     *  @return   string Method
     */
    public function getFormMethod()
    {
        return 'POST';
    }

    /**
     *  Array of hidden form fields (name => value)
     *
     *  @return   array
     */
    public function getFormFields()
    {
        return [];
    }

    /**
     *  Optimized getFormFields() method
     *
     *  @return   array
     */
    protected function _getFormFields()
    {
        if (!is_array($this->_formFields) || count($this->_formFields) == 0) {
            $this->_formFields = $this->getFormFields();
        }
        return $this->_formFields;
    }
}
