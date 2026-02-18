var ErrorTracker = (function () {
    var config = {
        endpoint: '/api/v1/capture',
        apiKey: null,
        debug: false
    };

    function log(message) {
        if (config.debug) {
            console.log('[ErrorTracker] ' + message);
        }
    }

    function sendError(data) {
        if (!config.apiKey) return;

        data.api_key = config.apiKey;
        data.url = window.location.href;
        data.ip = ''; // IP will be captured by server

        fetch(config.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data),
            keepalive: true
        }).then(function (response) {
            log('Error sent: ' + response.status);
        }).catch(function (err) {
            log('Failed to send error: ' + err);
        });
    }

    return {
        init: function (options) {
            config = Object.assign(config, options);
            log('Initialized with API Key: ' + (config.apiKey ? '***' : 'Missing'));

            // Capture Global Errors
            window.onerror = function (message, source, lineno, colno, error) {
                sendError({
                    message: message,
                    file: source,
                    line: lineno,
                    trace: error ? error.stack : null,
                    type: 'Javascript Error'
                });
            };

            // Capture Unhandled Promise Rejections
            window.onunhandledrejection = function (event) {
                sendError({
                    message: event.reason ? event.reason.toString() : 'Unhandled Promise Rejection',
                    type: 'Promise Rejection',
                    trace: event.reason ? event.reason.stack : null
                });
            };
        },
        capture: function (error, context) {
            sendError({
                message: error.message || error,
                type: 'Manual Capture',
                trace: error.stack || null,
                file: context ? context.file : null,
                line: context ? context.line : null
            });
        }
    };
})();
