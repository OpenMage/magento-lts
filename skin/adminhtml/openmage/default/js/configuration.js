/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 */
(() => {
    'use strict';

    const initialize = () => {
        const tabs = document.getElementById('system_config_tabs');
        const sidebar = document.getElementById('page:left');
        const content = document.getElementById('content');
        if (!tabs || !sidebar || !content || !document.body.classList.contains('adminhtml-system-config-edit')) {
            return;
        }

        const columns = sidebar.parentElement;
        const desktop = window.matchMedia('(min-width: 1000px) and (min-height: 640px)');
        const storageKey = 'openmage-config-navigation';
        const active = tabs.querySelector('a.active');
        let frame;

        // Use the existing translated headings to name the keyboard-scrollable panels.
        [sidebar, content].forEach((panel, index) => {
            const heading = panel.querySelector('h3');
            if (heading) {
                heading.id = heading.id || `config-panel-heading-${index}`;
                panel.setAttribute('role', 'region');
                panel.setAttribute('aria-labelledby', heading.id);
            }
        });

        const sizePanels = () => {
            document.body.classList.toggle('config-panels', desktop.matches);
            [sidebar, content].forEach(panel => {
                if (desktop.matches) {
                    panel.setAttribute('tabindex', '0');
                } else {
                    panel.removeAttribute('tabindex');
                }
            });
            if (!desktop.matches) {
                return;
            }
            const top = Math.max(0, columns.getBoundingClientRect().top);
            columns.style.setProperty('--config-panel-height', `${Math.max(320, window.innerHeight - top - 24)}px`);
            const header = content.querySelector('.content-header');
            if (header) {
                content.style.setProperty('--config-header-height', `${header.offsetHeight + 10}px`);
            }
        };
        const scheduleSize = () => {
            window.cancelAnimationFrame(frame);
            frame = window.requestAnimationFrame(sizePanels);
        };
        sizePanels();
        window.addEventListener('resize', scheduleSize);
        window.addEventListener('scroll', scheduleSize, {passive: true});

        // Store only a public section identifier and scroll coordinates for the next
        // navigation in this tab. Never store signed URLs or configuration values.
        let restored = false;
        try {
            const saved = JSON.parse(window.sessionStorage.getItem(storageKey) || 'null');
            window.sessionStorage.removeItem(storageKey);
            const age = saved ? Date.now() - saved.time : NaN;
            if (desktop.matches && saved && active && saved.section === active.id &&
                age >= 0 && age < 60000 && Number.isFinite(saved.top)) {
                sidebar.scrollTop = Math.max(0, saved.top);
                restored = true;
            }
        } catch (ignore) {
            // Disabled storage must not prevent navigation or reveal form data.
        }
        if (desktop.matches && active && !restored) {
            const position = active.getBoundingClientRect();
            const panel = sidebar.getBoundingClientRect();
            if (position.bottom > panel.bottom || position.top < panel.top) {
                sidebar.scrollTop += position.top - panel.top - 16;
            }
        }

        tabs.addEventListener('click', event => {
            if (!desktop.matches || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
                return;
            }
            const link = event.target.closest('a[id^="section-"]');
            if (!link || !tabs.contains(link)) {
                return;
            }
            try {
                window.sessionStorage.setItem(storageKey, JSON.stringify({
                    section: link.id,
                    top: sidebar.scrollTop,
                    time: Date.now(),
                }));
            } catch (ignore) {
                // Keep native links and form handling when storage is unavailable.
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();
