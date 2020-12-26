/* popup.js
 *
 * This file initializes its scripts after the popup has loaded.
 *
 * It shows how to access global variables from background.js.
 * Note that getViews could be used instead to access other scripts.
 *
 * A port to the active tab is open to send messages to its in-content.js script.
 *
 */

function isEnable() {
    var x = localStorage.getItem('redirect');
    if (typeof (x) === 'undefined') return 'md.vhn.vn';
    return x;
}

function enableRedirect(enable) {
    if (enable) {
        $('#redirect').val(enable);
        $('#enabled').css('display', 'block');
        localStorage.setItem('redirect', enable);
    } else {
        $('#redirect').val('');
        $('#enabled').css('display', 'none');
        localStorage.setItem('redirects', '');
    }
}

$(document).ready(function () {
    enableRedirect(isEnable());
    $('.btn-clear').on('click', function () {
        enableRedirect(false);
    })
    $('.btn-save').on('click', function () {
        enableRedirect($('#redirect').val().trim());
    })
});