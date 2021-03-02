/* scripts to run after page has loaded */
var callback = function () {
/* commands in this block execute after site finishes loading */
    /* run refreshViews function every 1 minute */
    //setInterval(refreshView, 60000);
};

/* event listener - runs launch callback launch function to run post-pageload operations */
if (
    document.readyState === 'complete' ||
    (document.readyState !== 'loading' && !document.documentElement.doScroll)
) {
    callback();
} else {
    document.addEventListener('DOMContentLoaded', callback);
}
