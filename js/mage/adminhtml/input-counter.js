window.addEventListener('DOMContentLoaded', function() {
    Element.addMethods({
        // setup once, memorize the counter element and maxLen
        prepare_for_countdown: function(element) {
            var elm = $(element);
            // even if you call it multiple times, it only works once
            if(!elm.retrieve('counter')) {
                var counter = new Element('span');
                elm.next('.note').insert(counter);
                elm.store('counter', counter);
                var maxLen = elm.className.match(/maximum-length-(\d+)/)[1];
                elm.store('maxLen', maxLen);
            }
            return elm; // so you can chain
        },
        // display the value, run once at load and on each observed keyup
        countdown: function(element) {
            var elm = $(element);
            var curLen = elm.getValue().length;
            var maxLen = elm.retrieve('maxLen');
            var count  = maxLen - curLen;
            var counter = elm.retrieve('counter');
            counter.update(' (' + curLen + '/' + maxLen + ')');
            if (curLen > maxLen) {
                counter.setStyle({'color': 'red'});
            } else {
                counter.setStyle({'color': 'inherit'});
            }
            return elm;
        }
    });

    // run setup and call countdown once outside of listener to initialize
    $$('.validate-length').invoke('prepare_for_countdown').invoke('countdown');

    // deferred listener, only responds to keyups that issue from a matching element
    document.on('keyup', '.validate-length', function(evt, elm) {
        elm.countdown();
    });
});
