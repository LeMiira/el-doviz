// Frontend JavaScript for El Doviz plugin

(function () {
    'use strict';

    // Pause ticker on hover and respect prefers-reduced-motion
    document.addEventListener('DOMContentLoaded', function () {
        var tickers = document.querySelectorAll('.el-doviz-ticker');
        if (!tickers.length) {
            return;
        }
        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduced) {
            // Disable animation by removing the CSS animation class
            tickers.forEach(function (t) {
                t.querySelector('span').style.animation = 'none';
            });
            return;
        }
        tickers.forEach(function (ticker) {
            ticker.addEventListener('mouseenter', function () {
                ticker.querySelector('span').style.animationPlayState = 'paused';
            });
            ticker.addEventListener('mouseleave', function () {
                ticker.querySelector('span').style.animationPlayState = 'running';
            });
        });
    });
})();
