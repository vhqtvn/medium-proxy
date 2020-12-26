
function isEnable() {
    return localStorage.getItem('redirect');
}

var enable = false;

setInterval(function () {
    enable = isEnable();
}, 500);

chrome.webRequest.onBeforeRequest.addListener(
    function (details) {
        if (!enable) return;
        var match = details.url.match("^https://(.*)\.medium.com($|/.*)");
        if (match) {
            return { redirectUrl: "https://" + match[1] + "-" + enable + match[2] };
        }
        match = details.url.match("^https://medium.com($|/.*)");
        if (match) {
            return { redirectUrl: "https://" + enable + match[1] };
        }
        return {};
    },
    {
        urls: [
            "<all_urls>",
        ],
    },
    ["blocking"]
);
