/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 * @package     rwd_default
 */

var ConfigurableMediaImages = {
    imageType: null,
    productImages: {},
    imageObjects: {},

    // deprecated - use Array.prototype.intersect instead
    arrayIntersect: function(a, b) {
        return a.intersect(b);
    },

    getCompatibleProductImages: function(productFallback, selectedLabels) {
        //find compatible products
        var compatibleProducts = [];
        var compatibleProductSets = [];
        selectedLabels.each(function(selectedLabel) {
            if(typeof(productFallback['option_labels']) != 'undefined') {
                if (!productFallback['option_labels'][selectedLabel]) {
                    return;
                }

                var optionProducts = productFallback['option_labels'][selectedLabel]['products'];
                compatibleProductSets.push(optionProducts);

                //optimistically push all products
                optionProducts.each(function (productId) {
                    compatibleProducts.push(productId);
                });
            }
        });

        //intersect compatible products
        compatibleProductSets.each(function(productSet) {
            compatibleProducts = ConfigurableMediaImages.arrayIntersect(compatibleProducts, productSet);
        });

        return compatibleProducts;
    },

    isValidImage: function(fallbackImageUrl) {
        if(!fallbackImageUrl) {
            return false;
        }

        return true;
    },

    getSwatchImage: function(productId, optionLabel, selectedLabels) {
        var fallback = ConfigurableMediaImages.productImages[productId];
        if(!fallback) {
            return null;
        }

        //first, try to get label-matching image on config product for this option's label
        if(typeof(fallback['option_labels']) != 'undefined') {
            var currentLabelImage = fallback['option_labels'][optionLabel];
            if (currentLabelImage && fallback['option_labels'][optionLabel]['configurable_product'][ConfigurableMediaImages.imageType]) {
                //found label image on configurable product
                return fallback['option_labels'][optionLabel]['configurable_product'][ConfigurableMediaImages.imageType];
            }
        }

        var compatibleProducts = ConfigurableMediaImages.getCompatibleProductImages(fallback, selectedLabels);

        if(compatibleProducts.length == 0) { //no compatible products
            return null; //bail
        }

        //second, get any product which is compatible with currently selected option(s)
        var optionLabels = fallback['option_labels'];
        for (var key in optionLabels) {
            if (optionLabels.hasOwnProperty(key)) {
                var value = optionLabels[key];
                var image = value['configurable_product'][ConfigurableMediaImages.imageType];
                var products = value['products'];

                if (image) { //configurable product has image in the first place
                    //if intersection between compatible products and this label's products, we found a match
                    var isCompatibleProduct = products.filter(function(productId) {
                        return compatibleProducts.includes(productId);
                    }).length > 0;

                    if (isCompatibleProduct) {
                        return image;
                    }
                }
            }
        }

        //third, get image off of child product which is compatible
        var childSwatchImage = null;
        var childProductImages = fallback[ConfigurableMediaImages.imageType];
        compatibleProducts.each(function(productId) {
            if(childProductImages[productId] && ConfigurableMediaImages.isValidImage(childProductImages[productId])) {
                childSwatchImage = childProductImages[productId];
                return false; //break "loop"
            }
        });
        if (childSwatchImage) {
            return childSwatchImage;
        }

        //fourth, get base image off parent product
        if (childProductImages[productId] && ConfigurableMediaImages.isValidImage(childProductImages[productId])) {
            return childProductImages[productId];
        }

        //no fallback image found
        return null;
    },

    getImageObject: function(productId, imageUrl) {
        var key = productId+'-'+imageUrl;
        if(!ConfigurableMediaImages.imageObjects[key]) {
            var image = document.createElement('img');
            image.src = imageUrl;
            ConfigurableMediaImages.imageObjects[key] = image;
        }
        return ConfigurableMediaImages.imageObjects[key];
    },

    updateImage(el) {
        var select = el;
        var label = select.options[select.selectedIndex].getAttribute('data-label');
        var productId = optionsPrice.productId; //get product ID from options price object

        //find all selected labels
        var selectedLabels = [];

        var superAttributeSelects = document.querySelectorAll('.product-options .super-attribute-select');
        superAttributeSelects.forEach(function(option) {
            if (option.value !== '') {
                selectedLabels.push(option.options[option.selectedIndex].getAttribute('data-label'));
            }
        });

        var swatchImageUrl = ConfigurableMediaImages.getSwatchImage(productId, label, selectedLabels);
        if (!ConfigurableMediaImages.isValidImage(swatchImageUrl)) {
            console.log('no image found');
            return;
        }

        var swatchImage = ConfigurableMediaImages.getImageObject(productId, swatchImageUrl);

        this.swapImage(swatchImage);
    },

    swapImage: function(targetImage) {
        targetImage.classList.add('gallery-image');

        var imageGallery = document.querySelector('.product-image-gallery');

        if (targetImage.complete) { // image already loaded -- swap immediately
            var galleryImages = imageGallery.querySelectorAll('.gallery-image');
            galleryImages.forEach(function(image) {
                image.classList.remove('visible');
            });

            // move target image to correct place, in case it's necessary
            imageGallery.appendChild(targetImage);

            // reveal new image
            targetImage.classList.add('visible');
        } else { // need to wait for image to load
            // add spinner
            imageGallery.classList.add('loading');

            // move target image to correct place, in case it's necessary
            imageGallery.appendChild(targetImage);

            // wait until image is loaded
            targetImage.addEventListener('load', function() {
                // remove spinner
                imageGallery.classList.remove('loading');

                // hide old image
                var galleryImages = imageGallery.querySelectorAll('.gallery-image');
                galleryImages.forEach(function(image) {
                    image.classList.remove('visible');
                });

                // reveal new image
                targetImage.classList.add('visible');
            });
        }
    },

    wireOptions: function() {
        var selectElements = document.querySelectorAll('.product-options .super-attribute-select');
        selectElements.forEach(function(selectElement) {
            selectElement.addEventListener('change', function(e) {
                ConfigurableMediaImages.updateImage(this);
            });
        });
    },

    swapListImage: function(productId, imageObject) {
        var originalImage = document.querySelector('#product-collection-image-' + productId);

        if (imageObject.complete) { // swap image immediately

            // remove old image
            originalImage.classList.add('hidden');
            document.querySelectorAll('.product-collection-image-' + productId).forEach(function (image) {
                image.remove();
            });

            // add new image
            originalImage.parentNode.insertBefore(imageObject, originalImage.nextSibling);

        } else { // need to load image

            var wrapper = originalImage.parentNode;

            // add spinner
            wrapper.classList.add('loading');

            // wait until image is loaded
            imageObject.addEventListener('load', function () {
                // remove spinner
                wrapper.classList.remove('loading');

                // remove old image
                originalImage.classList.add('hidden');
                document.querySelectorAll('.product-collection-image-' + productId).forEach(function (image) {
                    image.remove();
                });

                // add new image
                originalImage.parentNode.insertBefore(imageObject, originalImage.nextSibling);
            });

        }
    },

    swapListImageByOption: function(productId, optionLabel) {
        var swatchImageUrl = ConfigurableMediaImages.getSwatchImage(productId, optionLabel, [optionLabel]);
        if(!swatchImageUrl) {
            return;
        }

        var newImage = ConfigurableMediaImages.getImageObject(productId, swatchImageUrl);
        newImage.classList.add('product-collection-image-' + productId);

        ConfigurableMediaImages.swapListImage(productId, newImage);
    },

    setImageFallback: function(productId, imageFallback) {
        ConfigurableMediaImages.productImages[productId] = imageFallback;
    },

    init: function(imageType) {
        ConfigurableMediaImages.imageType = imageType;
        ConfigurableMediaImages.wireOptions();
    }
};
