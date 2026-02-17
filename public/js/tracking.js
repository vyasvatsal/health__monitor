// Error Tracking SDK (Enhanced)
(function (window) {
    const config = {
        endpoint: 'http://127.0.0.1:8000/api/v1/capture',
        storeId: null,
        debug: false
    };

    function init(options) {
        config.endpoint = options.endpoint || config.endpoint;
        config.storeId = options.storeId;
        config.debug = options.debug || false;

        if (!config.storeId) {
            console.error("Error Tracking: storeId is required");
            return;
        }

        if (config.debug) console.log("Error Tracking Initialized", config);

        // 1. Global JS Errors
        window.onerror = function (message, source, lineno, colno, error) {
            capture({
                type: error ? error.name : 'JavaScript Error',
                message: message,
                file: source,
                line: lineno,
                trace: error ? error.stack : null
            });
        };

        // 2. Unhandled Promise Rejections
        window.onunhandledrejection = function (event) {
            capture({
                type: 'UnhandledRejection',
                message: event.reason ? (event.reason.message || event.reason) : 'Unknown Rejection',
                trace: event.reason ? event.reason.stack : null
            });
        };

        // 3. Resource Loading Errors (img, script, link) - capture: true is key
        window.addEventListener('error', function (event) {
            // Filter out normal JS errors which are handled by onerror
            if (event.target === window) return;

            const element = event.target;
            const src = element.src || element.href;
            const tagName = element.tagName;

            if (src) {
                capture({
                    type: 'ResourceError',
                    message: `Failed to load resource: ${tagName} (${src})`,
                    file: src,
                    line: 0,
                    trace: element.outerHTML.substring(0, 200) // Context
                });
            }
        }, true);

        // 4. Network Errors (Fetch)
        const originalFetch = window.fetch;
        window.fetch = function () {
            return originalFetch.apply(this, arguments)
                .then(response => {
                    if (!response.ok && response.status >= 400) {
                        capture({
                            type: 'NetworkError',
                            message: `Fetch Failed: ${response.status} ${response.statusText}`,
                            file: arguments[0] instanceof Request ? arguments[0].url : arguments[0],
                            line: 0,
                            trace: `Status: ${response.status}\nURL: ${response.url}`
                        });
                    }
                    return response;
                })
                .catch(error => {
                    capture({
                        type: 'NetworkError',
                        message: `Fetch Connection Error: ${error.message}`,
                        file: arguments[0] instanceof Request ? arguments[0].url : arguments[0],
                        line: 0,
                        trace: error.stack
                    });
                    throw error;
                });
        };

        // 5. Network Errors (XHR)
        const originalXhrOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function (method, url) {
            this._url = url;
            this._method = method;
            return originalXhrOpen.apply(this, arguments);
        };

        const originalXhrSend = XMLHttpRequest.prototype.send;
        XMLHttpRequest.prototype.send = function () {
            const xhr = this;
            // Avoid recursion loop if we are sending to our own endpoint
            if (xhr._url && xhr._url.includes(config.endpoint)) {
                return originalXhrSend.apply(this, arguments);
            }

            xhr.addEventListener('error', function () {
                capture({
                    type: 'NetworkError',
                    message: `XHR Failed: Connection Error to ${xhr._url}`,
                    file: xhr._url,
                    line: 0,
                    trace: `Method: ${xhr._method}`
                });
            });

            xhr.addEventListener('load', function () {
                if (xhr.status >= 400) {
                    capture({
                        type: 'NetworkError',
                        message: `XHR Failed: ${xhr.status} ${xhr.statusText}`,
                        file: xhr._url,
                        line: 0,
                        trace: `Status: ${xhr.status}\nMethod: ${xhr._method}\nResponse: ${xhr.responseText.substring(0, 100)}`
                    });
                }
            });

            return originalXhrSend.apply(this, arguments);
        };
    }

    function capture(data) {
        if (!config.endpoint) return;

        const payload = JSON.stringify(data);

        if (config.debug) console.log("Capturing Error:", data);

        // Use sendBeacon for guarantees on page unload, fallback to fetch
        if (navigator.sendBeacon) {
            // sendBeacon doesn't support custom headers easily for JSON content-type in all browsers nicely, 
            // but we can try Blob.
            const blob = new Blob([payload], { type: 'application/json' });
            navigator.sendBeacon(config.endpoint, blob);
        } else {
            fetch(config.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: payload,
                keepalive: true
            }).catch(e => {
                if (config.debug) console.error("Error report failed:", e);
            });
        }
    }

    window.ErrorTracker = { init, capture };

})(window);
