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

        const helpLink = {
            'link': linkElement
        };

        let showPopup = false;

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
            const helpLinkIndex = this.helpLinks.push(helpLink) - 1;
            linkElement.addEventListener('click', this.showHelp.bind(this.helpLinks[helpLinkIndex]));
        }
        return helpLink;
    },

    /**
     * Replace the click handler this module installed on an element.
     * Mirrors stopObserving('click') without cloning the node, so callers
     * keep a live reference and unrelated listeners survive.
     */
    _setClickHandler: function(element, handler) {
        if (element._mapClickHandler) {
            element.removeEventListener('click', element._mapClickHandler);
        }
        element._mapClickHandler = handler;
        element.addEventListener('click', handler);
        return element;
    },

    setGotoView: function(element, viewPageUrl) {
        element.href = viewPageUrl;
        if(window.opener) {
            this._setClickHandler(element, function(event) {
                setPLocation(this.href,true);
                Catalog.Map.hideHelp();
                event.preventDefault();
                event.stopPropagation();
            });
        } else {
            this._setClickHandler(element, function(event) {
                setLocation(this.href);
                Catalog.Map.hideHelp();
                event.preventDefault();
                event.stopPropagation();
            });
        }
        return element;
    },

    showSelects: function() {
        const elements = document.getElementsByTagName("select");
        for (let i = 0; i < elements.length; i++) {
            elements[i].style.visibility='visible';
        }
    },

    hideSelects: function() {
        const elements = document.getElementsByTagName("select");
        for (let i = 0; i < elements.length; i++) {
            elements[i].style.visibility='hidden';
        }
    },

    showHelp: function(event) {
        const helpBox = document.getElementById('map-popup');
        if (!helpBox) {
            return;
        }

        //Move help box to be right in body tag
        const bodyNode = document.querySelector('body');
        if (helpBox.parentNode != bodyNode) {
            helpBox.remove();
            bodyNode.insertAdjacentElement('beforeend', helpBox);
            // Fix for FF4-FF5 bug with missing alt text after DOM manipulations
            const paypalImg = helpBox.querySelectorAll('.paypal-logo > a > img')[0];
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
            const mapTitle = document.getElementById('map-popup-heading');
            if (typeof this.title != 'undefined') {
                mapTitle.innerHTML = this.title;
                mapTitle.style.display = '';
            } else {
                mapTitle.style.display = 'none';
            }

            //MSRP price
            const mapMsrp = document.getElementById('map-popup-msrp-box');
            if (typeof this.msrp != 'undefined') {
                document.getElementById('map-popup-msrp').innerHTML = this.msrp;
                mapMsrp.style.display = '';
            } else {
                mapMsrp.style.display = 'none';
            }

            //Actual price
            const mapPrice = document.getElementById('map-popup-price-box');
            if (typeof this.price != 'undefined') {
                const price = typeof this.price == 'object' ? this.price.innerHTML : this.price;
                document.getElementById('map-popup-price').innerHTML = price;
                mapPrice.style.display = '';
            } else {
                mapPrice.style.display = 'none';
            }

            //`Add to cart` button
            const cartButton = document.getElementById('map-popup-button');
            if (typeof this.cartLink != 'undefined') {
                if (typeof productAddToCartForm == 'undefined' || this.notUseForm) {
                    Catalog.Map.setGotoView(cartButton, this.cartLink);
                    window.productAddToCartForm = document.getElementById('product_addtocart_form_from_popup');
                } else {
                    if (this.qty) {
                        productAddToCartForm.qty = this.qty;
                    }
                    cartButton.href = this.cartLink;
                    Catalog.Map._setClickHandler(cartButton, function () {
                        productAddToCartForm.action = this.href;
                        productAddToCartForm.submit(this);
                    });
                }
                productAddToCartForm.action = this.cartLink;
                const productField = document.getElementById('map-popup-product-id');
                productField.value = this.product_id;
                cartButton.style.display = '';
                document.querySelectorAll('.additional-addtocart-box').forEach(function(el) { el.style.display = ''; });
            } else {
                cartButton.style.display = 'none';
                document.querySelectorAll('.additional-addtocart-box').forEach(function(el) { el.style.display = 'none'; });
            }

            //Horizontal line
            const mapText = document.getElementById('map-popup-text'), mapTextWhatThis = document.getElementById('map-popup-text-what-this'), mapContent = document.getElementById('map-popup-content');
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
            const closeButton = document.getElementById('map-popup-close');
            if (closeButton) {
                Catalog.Map._setClickHandler(closeButton, Catalog.Map.showHelp.bind(this));
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
        const helpBox = document.getElementById('map-popup');
        if (helpBox) {
            helpBox.style.display = 'none';
            Catalog.Map.active = false;
        }
    },

    bindProductForm: function(){
        if (('undefined' != typeof productAddToCartForm) && productAddToCartForm) {
            productAddToCartFormOld = productAddToCartForm;
            window.productAddToCartForm = new VarienForm('product_addtocart_form_from_popup');
            productAddToCartForm.submitLight = productAddToCartFormOld.submitLight;
        } else if(!document.getElementById('product_addtocart_form_from_popup')) {
            return false;
        } else if ('undefined' == typeof productAddToCartForm) {
            window.productAddToCartForm = new VarienForm('product_addtocart_form_from_popup');
        }

        productAddToCartForm.submit = function(button, url) {
            if (('undefined' != typeof productAddToCartFormOld) && productAddToCartFormOld) {
                if (Catalog.Map.active) {
                    Catalog.Map.hideHelp();
                }
                if (productAddToCartForm.qty && document.getElementById('qty')) {
                    document.getElementById('qty').value = productAddToCartForm.qty;
                }
                productAddToCartFormOld.submit();
                return false;
            }
            if(window.opener) {
                const parentButton = button;
                fetch(this.form.action, {
                    method: 'POST',
                    body: new URLSearchParams({isAjax: 1, method: 'GET'}),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function(response) {
                    if (!response.ok) {
                        return;
                    }
                    window.opener.focus();
                    if (parentButton && parentButton.href) {
                        setPLocation(parentButton.href, true);
                        Catalog.Map.hideHelp();
                    }
                });
                return;
            }
            if (this.validator.validate()) {
                const form = this.form;
                const oldUrl = form.action;

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
    const data = event.detail, bundle = data.bundle;
    if (!Number(bundle.config.isMAPAppliedDirectly) && !Number(bundle.config.isFixedPrice)) {
        let canApplyMAP = false;
        try {
            for (const option in bundle.config.selected) {
                if (bundle.config.options[option] && bundle.config.options[option].selections) {
                    const selections = bundle.config.options[option].selections;
                    for (let i = 0, l = bundle.config.selected[option].length; i < l; i++) {
                        const selectionId = bundle.config.selected[option][i];
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
