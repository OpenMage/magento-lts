const counterMap = new WeakMap();

window.addEventListener('DOMContentLoaded', function() {
    // setup once, memorize the counter element and maxLen
    function prepareForCountdown(elm) {
        // even if you call it multiple times, it only works once
        if (!counterMap.has(elm)) {
            let counter = document.createElement('span');
            counter.classList.add('input-counter');
            elm.nextElementSibling.classList.add('note');
            elm.nextElementSibling.appendChild(counter);
            counterMap.set(elm, counter);
            let maxLen = elm.className.match(/maximum-length-(\d+)/)[1];
            elm.dataset.maxLen = maxLen;
        }
        return elm; // so you can chain
    }

    // display the value, run once at load and on each observed keyup
    function countdown(event) {
        let elm = this;
        let curLen = elm.value.length;
        let maxLen = elm.dataset.maxLen;
        let counter = counterMap.get(elm);
        counter.textContent = ` (${curLen}/${maxLen})`;
        if (curLen > maxLen) {
            counter.classList.add('input-counter-error');
        } else {
            counter.classList.remove('input-counter-error');
        }
        return elm; // so you can chain
    }

    document.querySelectorAll('.validate-length').forEach((elm) => {
        prepareForCountdown(elm);
        countdown.call(elm);
        elm.addEventListener('keyup', countdown);
        elm.addEventListener('paste', countdown);
        elm.addEventListener('propertychange', countdown);
    });
});