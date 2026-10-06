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

    arrayIntersect: function(a, b) {
        // Array#intersect de-duplicated the left-hand array first
        return a.filter(function(item, idx) {
            return a.indexOf(item) === idx && b.indexOf(item) !== -1;
        });
    },

    getCompatibleProductImages: function(productFallback, selectedLabels) {
        //find compatible products
        let compatibleProducts = [];
        const compatibleProductSets = [];
        selectedLabels.forEach(function(selectedLabel) {
            if(typeof(productFallback['option_labels']) != 'undefined') {
                if (!productFallback['option_labels'][selectedLabel]) {
                    return;
                }

                const optionProducts = productFallback['option_labels'][selectedLabel]['products'];
                compatibleProductSets.push(optionProducts);

                //optimistically push all products
                optionProducts.forEach(function(productId) {
                    compatibleProducts.push(productId);
                });
            }
        });

        //intersect compatible products
        compatibleProductSets.forEach(function(productSet) {
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
        const fallback = ConfigurableMediaImages.productImages[productId];
        if(!fallback) {
            return null;
        }

        //first, try to get label-matching image on config product for this option's label
        if(typeof(fallback['option_labels']) != 'undefined') {
            const currentLabelImage = fallback['option_labels'][optionLabel];
            if (currentLabelImage && fallback['option_labels'][optionLabel]['configurable_product'][ConfigurableMediaImages.imageType]) {
                //found label image on configurable product
                return fallback['option_labels'][optionLabel]['configurable_product'][ConfigurableMediaImages.imageType];
            }
        }

        const compatibleProducts = ConfigurableMediaImages.getCompatibleProductImages(fallback, selectedLabels);

        if(compatibleProducts.length == 0) { //no compatible products
            return null; //bail
        }

        //second, get any product which is compatible with currently selected option(s)
        const optionLabels = fallback['option_labels'];
        for (const key in optionLabels) {
            if (optionLabels.hasOwnProperty(key)) {
                const value = optionLabels[key];
                const image = value['configurable_product'][ConfigurableMediaImages.imageType];
                const products = value['products'];

                if (image) { //configurable product has image in the first place
                    //if intersection between compatible products and this label's products, we found a match
                    const isCompatibleProduct = products.filter(function(productId) {
                        return compatibleProducts.includes(productId);
                    }).length > 0;

                    if (isCompatibleProduct) {
                        return image;
                    }
                }
            }
        }

        //third, get image off of child product which is compatible
        // NB: the original Prototype code used `each(... return false)`, which does NOT break the
        // loop (only `throw $break` does), so it iterated all compatible products and the LAST
        // match won. Preserve that behaviour here — do not break on the first match.
        let childSwatchImage = null;
        const childProductImages = fallback[ConfigurableMediaImages.imageType];
        compatibleProducts.forEach(function(childId) {
            if(childProductImages[childId] && ConfigurableMediaImages.isValidImage(childProductImages[childId])) {
                childSwatchImage = childProductImages[childId];
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
        const key = productId+'-'+imageUrl;
        if(!ConfigurableMediaImages.imageObjects[key]) {
            const image = document.createElement('img');
            image.src = imageUrl;
            ConfigurableMediaImages.imageObjects[key] = image;
        }
        return ConfigurableMediaImages.imageObjects[key];
    },

    updateImage(el) {
        const select = el;
        const label = select.options[select.selectedIndex].getAttribute('data-label');
        const productId = optionsPrice.productId; //get product ID from options price object

        //find all selected labels
        const selectedLabels = [];

        const superAttributeSelects = document.querySelectorAll('.product-options .super-attribute-select');
        superAttributeSelects.forEach(function(option) {
            if (option.value !== '') {
                selectedLabels.push(option.options[option.selectedIndex].getAttribute('data-label'));
            }
        });

        const swatchImageUrl = ConfigurableMediaImages.getSwatchImage(productId, label, selectedLabels);
        if (!ConfigurableMediaImages.isValidImage(swatchImageUrl)) {
            console.log('no image found');
            return;
        }

        const swatchImage = ConfigurableMediaImages.getImageObject(productId, swatchImageUrl);

        this.swapImage(swatchImage);
    },

    swapImage: function(targetImage) {
        targetImage.classList.add('gallery-image');

        ProductMediaManager.destroyZoom();

        const imageGallery = document.querySelector('.product-image-gallery');

        if (targetImage.complete) { // image already loaded -- swap immediately
            const galleryImages = imageGallery.querySelectorAll('.gallery-image');
            galleryImages.forEach(function(image) {
                image.classList.remove('visible');
            });

            // move target image to correct place, in case it's necessary
            imageGallery.appendChild(targetImage);

            // reveal new image
            targetImage.classList.add('visible');

            ProductMediaManager.createZoom($j(targetImage));
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
                const galleryImages = imageGallery.querySelectorAll('.gallery-image');
                galleryImages.forEach(function(image) {
                    image.classList.remove('visible');
                });

                // reveal new image
                targetImage.classList.add('visible');

                ProductMediaManager.createZoom($j(targetImage));
            });
        }
    },

    wireOptions: function() {
        const selectElements = document.querySelectorAll('.product-options .super-attribute-select');
        selectElements.forEach(function(selectElement) {
            selectElement.addEventListener('change', function(e) {
                ConfigurableMediaImages.updateImage(this);
            });
        });
    },

    swapListImage: function(productId, imageObject) {
        const originalImage = document.querySelector('#product-collection-image-' + productId);

        if (imageObject.complete) { // swap image immediately

            // remove old image
            originalImage.classList.add('hidden');
            document.querySelectorAll('.product-collection-image-' + productId).forEach(function (image) {
                image.remove();
            });

            // add new image
            originalImage.parentNode.insertBefore(imageObject, originalImage.nextSibling);

        } else { // need to load image

            const wrapper = originalImage.parentNode;

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
        const swatchImageUrl = ConfigurableMediaImages.getSwatchImage(productId, optionLabel, [optionLabel]);
        if(!swatchImageUrl) {
            return;
        }

        const newImage = ConfigurableMediaImages.getImageObject(productId, swatchImageUrl);
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
