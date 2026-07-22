/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 * @package     rwd_default
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 */

Checkout.prototype.gotoSection = function (section, reloadProgressBlock) {
    // Adds class so that the page can be styled to only show the "Checkout Method" step
    if ((this.currentStep == 'login' || this.currentStep == 'billing') && section == 'billing') {
        if (typeof $j !== 'undefined') {
            $j('body').addClass('opc-has-progressed-from-login');
        } else {
            document.body.classList.add('opc-has-progressed-from-login');
        }
    }

    if (reloadProgressBlock) {
        this.reloadProgressBlock(this.currentStep);
    }
    this.currentStep = section;
    var sectionElement = document.getElementById('opc-' + section);
    sectionElement.classList.add('allow');
    this.accordion.openSection('opc-' + section);

    // Scroll viewport to top of checkout steps for smaller viewports
    if (typeof Modernizr !== 'undefined' && typeof bp !== 'undefined' && Modernizr.mq('(max-width: ' + bp.xsmall + 'px)')) {
        if (typeof $j !== 'undefined') {
            $j('html,body').animate({scrollTop: $j('#checkoutSteps').offset().top}, 800);
        }
    }

    if (!reloadProgressBlock) {
        this.resetPreviousSteps();
    }
};
