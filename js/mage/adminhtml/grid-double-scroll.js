class gridDoubleScroll {

    _scrolling = false;

    constructor(wrapperScrollBar) {
        this.init(wrapperScrollBar);   
    }

    init(wrapperScrollBar) {
        this.wrapperScrollBar = wrapperScrollBar;
        let scrollbarTop = this.wrapperScrollBar.parentNode.querySelector('.hor-scroll-top');
        if (!scrollbarTop){
            this.createDoubleScroll(this.wrapperScrollBar);
            this.scrollbarTop.addEventListener('scroll', this.syncWrapperScrollBar.bind(this), false);
            this.wrapperScrollBar.addEventListener('scroll', this.syncScrollBarTop.bind(this), false);
            const observer = new MutationObserver( this.updateDoubleScrollWidth.bind(this) );
            observer.observe(this.wrapperScrollBar, { childList: true, subtree: true });
        }
        this.updateDoubleScrollWidth();
    }

    createDoubleScroll() {
        let scrollbarTop = document.createElement('div');
        scrollbarTop.classList.add('hor-scroll-top');
        scrollbarTop.appendChild(document.createElement('div'));
        scrollbarTop.style.overflow = 'auto';
        scrollbarTop.style.overflowY = 'hidden';
        //scrollbarTop.firstChild.style.height = '0';
        scrollbarTop.firstChild.style.paddingTop = '1px';
        scrollbarTop.firstChild.appendChild(document.createTextNode('\xA0'));
        this.wrapperScrollBar.parentNode.insertBefore(scrollbarTop, this.wrapperScrollBar);

        this.scrollbarTop = scrollbarTop;
    }

    syncWrapperScrollBar() {
        if(this._scrolling) {
            this._scrolling = false;
            return;
        }
        this._scrolling = true;
        this.wrapperScrollBar.scrollLeft = this.scrollbarTop.scrollLeft;
    }

    syncScrollBarTop() {
        if(this._scrolling) {
            this._scrolling = false;
            return;
        }
        this._scrolling = true;
        this.scrollbarTop.scrollLeft = this.wrapperScrollBar.scrollLeft;
    };

    updateDoubleScrollWidth() {
        if (this.scrollbarTop.firstChild.style.width != this.wrapperScrollBar.scrollWidth) {
            this.scrollbarTop.firstChild.style.width = this.wrapperScrollBar.scrollWidth + 'px';
        }
    }
};

window.addEventListener('DOMContentLoaded', function() {
    let horScrolls = document.querySelectorAll('.hor-scroll');
    horScrolls.forEach( (horScroll) => {
        new gridDoubleScroll(horScroll);
    });
});
