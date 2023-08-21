window.addEventListener('DOMContentLoaded', function() {
    function DoubleScroll(element) {
        if(!element.getAttribute('data-doublescroll')){
            var scrollbar= document.createElement('div');
            scrollbar.appendChild(document.createElement('div'));
            scrollbar.style.overflow= 'auto';
            scrollbar.style.overflowY= 'hidden';
            scrollbar.firstChild.style.width= element.scrollWidth+'px';
            scrollbar.firstChild.style.height= '0';
            scrollbar.firstChild.style.paddingTop= '1px';
            scrollbar.firstChild.appendChild(document.createTextNode('\xA0'));
            var running = false;
            scrollbar.onscroll= function() {
                if(running) {
                    running = false;
                    return;
                }
                running = true;
                element.scrollLeft= scrollbar.scrollLeft;
            };
            element.onscroll= function() {
                if(running) {
                    running = false;
                    return;
                }
                running = true;
                scrollbar.scrollLeft= element.scrollLeft;
            };
            element.parentNode.insertBefore(scrollbar, element);
            element.setAttribute('data-doublescroll', 1);
        }
    }
    var horscrolls = document.querySelectorAll('.hor-scroll');
    for (horscroll of horscrolls) {    
        DoubleScroll(horscroll);
    }
    const config = { childList: true };
    const callback = function(mutationsList, observer) {
        var horscrolls = document.querySelectorAll('.hor-scroll');
        for (horscroll of horscrolls) {      
            DoubleScroll(horscroll);
        }
    };
    const observer = new MutationObserver(callback);
    const productGridNode = document.getElementById('productGrid');
    if(productGridNode){
        observer.observe(productGridNode, config);
    }
    const salesOrderGridNode = document.getElementById('sales_order_grid');
    if(salesOrderGridNode){
        observer.observe(salesOrderGridNode, config);
    }
});
