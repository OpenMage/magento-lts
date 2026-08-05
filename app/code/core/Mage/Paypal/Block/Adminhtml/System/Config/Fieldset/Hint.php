<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Renderer for PayPal banner in System Configuration
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Hint extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'paypal/system/config/fieldset/hint.phtml';

    /**
     * Render fieldset html
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $elementOriginalData = $element->getOriginalData();
        if (isset($elementOriginalData['help_link'])) {
            $this->setHelpLink($elementOriginalData['help_link']);
        }

        $str = '
            paypalToggleSolution = function(id, url) {
                var doScroll = false;
                Fieldset.toggleCollapse(id, url);
                if (this.classList.contains("open")) {
                    document.querySelectorAll(".with-button button.button").forEach(function(anotherButton) {
                        if (anotherButton != this && anotherButton.classList.contains("open")) {
                            anotherButton.click();
                            doScroll = true;
                        }
                    }.bind(this));
                }
                if (doScroll) {
                    var rect = this.getBoundingClientRect();
                    window.scrollTo(rect.left + window.pageXOffset, rect.top + window.pageYOffset - 45);
                }
            };

            togglePaypalSolutionConfigureButton = function(button, enable) {
                button.disabled = !enable;
                if (button.classList.contains("disabled") && enable) {
                    button.classList.remove("disabled");
                } else if (!button.classList.contains("disabled") && !enable) {
                    button.classList.add("disabled");
                }
            };

            // check store-view disabling Express Checkout
            (function() {
                var run = function() {
                    var ecButton = document.querySelectorAll(".pp-method-express button.button")[0];
                    var ecEnabler = document.querySelectorAll(".paypal-ec-enabler")[0];
                    if (typeof ecButton == "undefined" || typeof ecEnabler != "undefined") {
                        return;
                    }
                    document.querySelectorAll(".with-button button.button").forEach(function(configureButton) {
                        if (configureButton != ecButton && !configureButton.disabled
                            && !configureButton.classList.contains("paypal-ec-separate")
                        ) {
                            togglePaypalSolutionConfigureButton(ecButton, false);
                        }
                    });
                };
                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", run);
                } else {
                    run();
                }
            })();
        ';

        /** @var Mage_Adminhtml_Helper_Js $helper */
        $helper = $this->helper('adminhtml/js');
        return $this->toHtml() . $helper->getScript($str);
    }
}
