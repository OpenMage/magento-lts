/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 * @package     base_default
 */
if (!window.Catalog) {
    window.Catalog = {};
}

Catalog.Map = {

    helpLinks: [],

    active: false,

    addHelpLink: function(linkElement, title, actualPrice, msrpPrice, addToCartLink) {
        if (typeof linkElement == 'string') {
            linkElement = document.querySelectorAll(linkElement)[0];
        }

        if (!linkElement) {
            return;
        }

        var helpLink = {
            'link': linkElement
        };

        var showPopup = false;

        if (typeof title == 'string' && title) {
            helpLink.title = title;
            showPopup = true;
        }

        if (typeof actualPrice == 'string' && actualPrice || typeof actualPrice == 'object' && actualPrice) {
            helpLink.price = actualPrice;
            showPopup = true;
        }

        if (typeof msrpPrice == 'string' && msrpPrice) {
            helpLink.msrp = msrpPrice;
            showPopup = true;
        }

        if (typeof addToCartLink == 'string' && addToCartLink) {
            helpLink.cartLink = addToCartLink;
        } else if (addToCartLink && addToCartLink.url) {
            helpLink.cartLink = addToCartLink.url;
            if (addToCartLink.qty) {
                helpLink.qty = addToCartLink.qty;
            }
            if (addToCartLink.notUseForm) {
                helpLink.notUseForm = addToCartLink.notUseForm;
            }
        }

        if (!showPopup) {
            this.setGotoView(linkElement, addToCartLink);
        } else {
            var helpLinkIndex = this.helpLinks.push(helpLink) - 1;
            linkElement.addEventListener('click', this.showHelp.bind(this.helpLinks[helpLinkIndex]));
        }
        return helpLink;
    },

    setGotoView: function(element, viewPageUrl) {
        var clone = element.cloneNode(true);
        element.parentNode.replaceChild(clone, element);
        element = clone;
        element.href = viewPageUrl;
        if(window.opener) {
            element.addEventListener('click', function(event) {
                setPLocation(this.href,true);
                Catalog.Map.hideHelp();
                event.preventDefault();
                event.stopPropagation();
            });
        } else {
            element.addEventListener('click', function(event) {
                setLocation(this.href);
                Catalog.Map.hideHelp();
                event.preventDefault();
                event.stopPropagation();
            });
        }
    },

    showSelects: function() {
        var elements = document.getElementsByTagName("select");
        for (i=0;i< elements.length;i++) {
            elements[i].style.visibility='visible';
        }
    },

    hideSelects: function() {
        var elements = document.getElementsByTagName("select");
        for (i=0;i< elements.length;i++) {
            elements[i].style.visibility='hidden';
        }
    },

    showHelp: function(event) {
        var helpBox = document.getElementById('map-popup');
        if (!helpBox) {
            return;
        }

        //Move help box to be right in body tag
        var bodyNode = document.querySelector('body');
        if (helpBox.parentNode != bodyNode) {
            helpBox.remove();
            bodyNode.insertAdjacentElement('beforeend', helpBox);
            // Fix for FF4-FF5 bug with missing alt text after DOM manipulations
            var paypalImg = helpBox.querySelectorAll('.paypal-logo > a > img')[0];
            if (paypalImg) paypalImg.src = paypalImg.src;
        }

        if (this != Catalog.Map && Catalog.Map.active != this.link) {
            helpBox.style.display = 'none';
            if (!helpBox.offsetPosition) {
                helpBox.offsetPosition = {left:0, top: 0};
            }

            helpBox.classList.remove('map-popup-right');
            helpBox.classList.remove('map-popup-left');
            if (bodyNode.offsetWidth < event.pageX + helpBox.offsetWidth) {
                helpBox.classList.add('map-popup-left');
            } else if (event.pageX - helpBox.offsetWidth < 0) {
                helpBox.classList.add('map-popup-right');
            }

            helpBox.style.left = event.pageX - (helpBox.offsetWidth / 2) + 'px';
            helpBox.style.top = event.pageY + 10 + 'px';

            //Title
            var mapTitle = document.getElementById('map-popup-heading');
            if (typeof this.title != 'undefined') {
                mapTitle.innerHTML = this.title;
                mapTitle.style.display = '';
            } else {
                mapTitle.style.display = 'none';
            }

            //MSRP price
            var mapMsrp = document.getElementById('map-popup-msrp-box');
            if (typeof this.msrp != 'undefined') {
                document.getElementById('map-popup-msrp').innerHTML = this.msrp;
                mapMsrp.style.display = '';
            } else {
                mapMsrp.style.display = 'none';
            }

            //Actual price
            var mapPrice = document.getElementById('map-popup-price-box');
            if (typeof this.price != 'undefined') {
                var price = typeof this.price == 'object' ? this.price.innerHTML : this.price;
                document.getElementById('map-popup-price').innerHTML = price;
                mapPrice.style.display = '';
            } else {
                mapPrice.style.display = 'none';
            }

            //`Add to cart` button
            var cartButton = document.getElementById('map-popup-button');
            if (typeof this.cartLink != 'undefined') {
                if (typeof productAddToCartForm == 'undefined' || this.notUseForm) {
                    Catalog.Map.setGotoView(cartButton, this.cartLink);
                    productAddToCartForm = document.getElementById('product_addtocart_form_from_popup');
                } else {
                    if (this.qty) {
                        productAddToCartForm.qty = this.qty;
                    }
                    var cartClone = cartButton.cloneNode(true);
                    cartButton.parentNode.replaceChild(cartClone, cartButton);
                    cartButton = cartClone;
                    cartButton.href = this.cartLink;
                    cartButton.addEventListener('click', function () {
                        productAddToCartForm.action = this.href;
                        productAddToCartForm.submit(this);
                    });
                }
                productAddToCartForm.action = this.cartLink;
                var productField = document.getElementById('map-popup-product-id');
                productField.value = this.product_id;
                cartButton.style.display = '';
                document.querySelectorAll('.additional-addtocart-box').forEach(function(el) { el.style.display = ''; });
            } else {
                cartButton.style.display = 'none';
                document.querySelectorAll('.additional-addtocart-box').forEach(function(el) { el.style.display = 'none'; });
            }

            //Horizontal line
            var mapText = document.getElementById('map-popup-text'),
                mapTextWhatThis = document.getElementById('map-popup-text-what-this'),
                mapContent = document.getElementById('map-popup-content');
            if (mapMsrp.style.display === 'none' && mapPrice.style.display === 'none' && cartButton.style.display === 'none') {
                //If just `What's this?` link
                mapText.style.display = 'none';
                mapTextWhatThis.style.display = '';
                mapTextWhatThis.classList.remove('map-popup-only-text');
                mapContent.style.display = 'none';
                mapContent.style.visibility = 'hidden';
                document.getElementById('product_addtocart_form_from_popup').style.display = 'none';
            } else {
                mapTextWhatThis.style.display = 'none';
                mapText.style.display = '';
                mapText.classList.add('map-popup-only-text');
                mapContent.style.display = '';
                mapContent.style.visibility = 'visible';
                document.getElementById('product_addtocart_form_from_popup').style.display = '';
            }

            helpBox.style.display = '';
            var closeButton = document.getElementById('map-popup-close');
            if (closeButton) {
                var closeClone = closeButton.cloneNode(true);
                closeButton.parentNode.replaceChild(closeClone, closeButton);
                closeClone.addEventListener('click', Catalog.Map.showHelp.bind(this));
                Catalog.Map.active = this.link;
            }
        } else {
            helpBox.style.display = 'none';
            Catalog.Map.active = false;
        }

        event.preventDefault();
        event.stopPropagation();
    },

    hideHelp: function(){
        var helpBox = document.getElementById('map-popup');
        if (helpBox) {
            helpBox.style.display = 'none';
            Catalog.Map.active = false;
        }
    },

    bindProductForm: function(){
        if (('undefined' != typeof productAddToCartForm) && productAddToCartForm) {
            productAddToCartFormOld = productAddToCartForm;
            productAddToCartForm = new VarienForm('product_addtocart_form_from_popup');
            productAddToCartForm.submitLight = productAddToCartFormOld.submitLight;
        } else if(!document.getElementById('product_addtocart_form_from_popup')) {
            return false;
        } else if ('undefined' == typeof productAddToCartForm) {
            productAddToCartForm = new VarienForm('product_addtocart_form_from_popup');
        }

        productAddToCartForm.submit = function(button, url) {
            if (('undefined' != typeof productAddToCartFormOld) && productAddToCartFormOld) {
                if (Catalog.Map.active) {
                    Catalog.Map.hideHelp();
                }
                if (productAddToCartForm.qty && document.getElementById('qty')) {
                    document.getElementById('qty').value = productAddToCartForm.qty;
                }
                parentResult = productAddToCartFormOld.submit();
                return false;
            }
            if(window.opener) {
                var parentButton = button;
                fetch(this.form.action, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function() {
                    window.opener.focus();
                    if (parentButton && parentButton.href) {
                        setPLocation(parentButton.href, true);
                        Catalog.Map.hideHelp();
                    }
                });
                return;
            }
            if (this.validator.validate()) {
                var form = this.form;
                var oldUrl = form.action;

                if (url) {
                   form.action = url;
                }
                if (!form.getAttribute('action')) {
                   form.action = productAddToCartForm.action;
                }
                try {
                    this.form.submit();
                } catch (e) {
                    this.form.action = oldUrl;
                    throw e;
                }
                this.form.action = oldUrl;

                if (button && button != 'undefined') {
                    button.disabled = true;
                }
            }
        };
    }
};

window.addEventListener('resize', function(event) {
    if (Catalog.Map.active) {
        Catalog.Map.showHelp(event);
    }
});

document.addEventListener('bundle:reload-price', function (event) { //reload price
    var data = event.detail, bundle = data.bundle;
    if (!Number(bundle.config.isMAPAppliedDirectly) && !Number(bundle.config.isFixedPrice)) {
        var canApplyMAP = false;
        try {
            for (var option in bundle.config.selected) {
                if (bundle.config.options[option] && bundle.config.options[option].selections) {
                    var selections = bundle.config.options[option].selections;
                    for (var i = 0, l = bundle.config.selected[option].length; i < l; i++) {
                        var selectionId = bundle.config.selected[option][i];
                        if (Number(selections[selectionId].canApplyMAP)) {
                            canApplyMAP = true;
                            break;
                        }
                    }
                }
                if (canApplyMAP) {
                    break;
                }
            }
        } catch (e) {
            canApplyMAP = true;
        }
        if (canApplyMAP) {
            document.querySelectorAll('.full-product-price').forEach(function(e){
                e.style.display = 'none';
            });
            document.querySelectorAll('.map-info').forEach(function(e){
                e.style.display = '';
            });
            event.detail.noReloadPrice = true;
        } else {
            document.querySelectorAll('.full-product-price').forEach(function(e){
                e.style.display = '';
            });
            document.querySelectorAll('.map-info').forEach(function(e){
                e.style.display = 'none';
            });
        }
    }
});
