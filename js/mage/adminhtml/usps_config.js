/**
 * USPS Configuration JavaScript
 * 
 * Automatically populates the REST API Gateway URL field based on 
 * the selected REST API Environment (Production/Sandbox).
 */

var UspsConfig = {
    // URL mappings for each environment
    urls: {
        'production': 'https://apis.usps.com/',
        'sandbox': 'https://apis-tem.usps.com/'
    },

    /**
     * Initialize the configuration handler
     */
    init: function() {
        // Get the environment dropdown element
        var envField = document.getElementById('carriers_usps_environment');
        var urlField = document.getElementById('carriers_usps_gateway_url');
        
        if (!envField || !urlField) {
            return; // Fields not present on this page
        }

        // Set initial URL based on current environment selection
        this.updateUrl(envField, urlField);

        // Add change event listener
        var self = this;
        Event.observe(envField, 'change', function() {
            self.updateUrl(envField, urlField);
        });
    },

    /**
     * Update the URL field based on selected environment
     */
    updateUrl: function(envField, urlField) {
        var selectedEnv = envField.value;
        var newUrl = this.urls[selectedEnv];
        
        if (newUrl) {
            urlField.value = newUrl;
            
            // Add visual feedback (optional)
            urlField.style.backgroundColor = '#ffffcc';
            setTimeout(function() {
                urlField.style.backgroundColor = '';
            }, 500);
        }
    }
};

// Initialize when DOM is ready
document.observe('dom:loaded', function() {
    UspsConfig.init();
});
