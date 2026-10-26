/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Academic Free License (AFL 3.0)
 * @package     rwd_default
 */

$j(document).ready(function () {

    // ==============================================
    // UI Pattern - Slideshow
    // ==============================================

    $j('.slideshow-container .slideshow')
        .cycle({
            slides: '> li',
            pager: '.slideshow-pager',
            pagerTemplate: '<span class="pager-box"></span>',
            speed: 600,
            pauseOnHover: true,
            swipe: true,
            prev: '.slideshow-prev',
            next: '.slideshow-next',
            fx: 'scrollHorz'
        });
});
