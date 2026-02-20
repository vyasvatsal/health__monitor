/**
 * Health Monitor Client SDK
 * Captures JavaScript errors and performance metrics.
 */
(function (window) {
    'use strict';

    const CONFIG = {
        endpoint: 'http://localhost:8000/api/v1/track', // Default, should be overridden
        publicKey: null,
        debug: false
    };

    const CONTEXT = {
        device: {
            userAgent: navigator.userAgent,
            language: navigator.language,
            platform: navigator.platform,
            screen: {
                width: window.screen.width,
                height: window.screen.height
            }
        },
        tags: []
    };

    function log(message, data) {
        if (CONFIG.debug) {
            console.log(`[Monitor SDK] ${message}`, data || '');
        }
    }

    function sendPayload(payload) {
        if (!CONFIG.publicKey) {
            log('Public Key not configured. Dropping payload.');
            return;
        }

        fetch(CONFIG.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Monitor-Key': CONFIG.publicKey,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        }).then(response => {
            if (!response.ok) {
                log('Failed to send payload', response.statusText);
            } else {
                log('Payload sent successfully');
            }
        }).catch(error => {
            log('Network error sending payload', error);
        });
    }

    function captureException(error, context = {}) {
        const payload = {
            exception: {
                type: error.name || 'Error',
                message: error.message || 'Unknown Error',
                file: error.fileName || error.sourceURL || 'unknown', // Non-standard but common
                line: error.lineNumber || error.line || 0, // Non-standard but common
                trace: error.stack || 'No stack trace available'
            },
            context: { ...CONTEXT, ...context },
            device: CONTEXT.device,
            tags: CONTEXT.tags
        };

        sendPayload(payload);
    }

    // Global Error Handler (Runtime JS Errors)
    window.onerror = function (message, source, lineno, colno, error) {
        captureException({
            message: message,
            fileName: source,
            lineNumber: lineno,
            stack: error ? error.stack : null,
            name: error ? error.name : 'WindowError'
        }, { type: 'javascript_error' });
        return false; // Let default handler run
    };

    // Unhandled Promise Rejection
    window.onunhandledrejection = function (event) {
        captureException({
            message: 'Unhandled Promise Rejection: ' + (event.reason ? (event.reason.message || event.reason) : 'Unknown'),
            name: 'UnhandledRejection',
            stack: event.reason ? event.reason.stack : null
        }, { type: 'promise_rejection' });
    };

    // Resource Loading Errors (Images, Scripts, CSS)
    window.addEventListener('error', function (event) {
        // Filter out WindowError which is handled by window.onerror
        if (event.target && (event.target.src || event.target.href)) {
            captureException({
                message: `Failed to load resource: ${event.target.src || event.target.href}`,
                name: 'ResourceError',
                fileName: window.location.href,
                lineNumber: 0,
                stack: null
            }, {
                type: 'resource_error',
                resource_tag: event.target.tagName.toLowerCase(),
                resource_url: event.target.src || event.target.href
            });
        }
    }, true); // Use capture phase

    // API Interceptors (Fetch)
    if (window.fetch) {
        const originalFetch = window.fetch;
        window.fetch = async function () {
            const url = arguments[0];
            try {
                const response = await originalFetch.apply(this, arguments);
                if (!response.ok) {
                    captureException({
                        message: `HTTP Error: ${response.status} ${response.statusText} on ${url}`,
                        name: 'FetchError',
                        fileName: url,
                        lineNumber: 0,
                        stack: null
                    }, {
                        type: 'network_error',
                        http_status: response.status,
                        http_method: arguments[1] ? arguments[1].method || 'GET' : 'GET'
                    });
                }
                return response;
            } catch (error) {
                // If fetch completely fails (e.g. CORS, Network down)
                captureException({
                    message: `Network Request Failed to ${url}: ${error.message}`,
                    name: 'FetchFailed',
                    fileName: url,
                    lineNumber: 0,
                    stack: error.stack
                }, {
                    type: 'network_error',
                    http_method: arguments[1] ? arguments[1].method || 'GET' : 'GET'
                });
                throw error;
            }
        };
    }

    // Public API
    window.HealthMonitor = {
        init: function (options) {
            if (options.endpoint) CONFIG.endpoint = options.endpoint;
            if (options.publicKey) CONFIG.publicKey = options.publicKey;
            if (options.debug) CONFIG.debug = options.debug;

            log('Initialized with config', CONFIG);
        },
        captureException: captureException,
        context: function (key, value) {
            CONTEXT[key] = value;
        }
    };

})(window);
