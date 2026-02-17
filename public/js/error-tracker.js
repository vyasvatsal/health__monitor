(function (window) {
    'use strict';

    const ErrorTracker = {
        config: {
            storeId: null,
            endpoint: '/api/v1/monitor/',
            debug: false
        },

        init: function (config) {
            this.config = { ...this.config, ...config };
            this.registerGlobalHandlers();
            if (this.config.debug) {
                console.log('ErrorTracker initialized for store:', this.config.storeId);
            }
        },

        registerGlobalHandlers: function () {
            const self = this;

            window.onerror = function (message, source, lineno, colno, error) {
                self.capture({
                    message: message,
                    stack_trace: error ? error.stack : `${source}:${lineno}:${colno}`,
                    source: source,
                    lineno: lineno,
                    colno: colno,
                    type: 'uncaught_exception'
                });
            };

            window.onunhandledrejection = function (event) {
                self.capture({
                    message: event.reason ? (event.reason.message || event.reason) : 'Unhandled Promise Rejection',
                    stack_trace: event.reason ? event.reason.stack : null,
                    type: 'unhandled_rejection'
                });
            };
        },

        capture: function (data) {
            if (!this.config.storeId) {
                console.error('ErrorTracker: storeId is not configured.');
                return;
            }

            const payload = {
                message: data.message,
                stack_trace: data.stack_trace,
                payload: {
                    url: window.location.href,
                    userAgent: navigator.userAgent,
                    ...data
                }
            };

            fetch(`${this.config.endpoint}${this.config.storeId}/capture`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            }).then(response => {
                if (this.config.debug) {
                    console.log('ErrorTracker: Error report sent', response);
                }
            }).catch(err => {
                console.error('ErrorTracker: Failed to send error report', err);
            });
        }
    };

    window.ErrorTracker = ErrorTracker;

})(window);
