/**
 * Health Monitor Client SDK
 * 
 * Usage: 
 * <script src="https://your-monitor-domain.com/sdk/tracker.js"></script>
 * <script>
 *   HealthMonitor.init({
 *     apiKey: 'YOUR_STORE_API_KEY',
 *     endpoint: 'https://your-monitor-domain.com/api/telemetry'
 *   });
 * </script>
 */

(function (window) {
    'use strict';

    const HealthMonitor = {
        config: {
            apiKey: null,
            endpoint: '/api/v1/telemetry', // Default if not provided
            maxQueueSize: 10,
            flushInterval: 5000 // 5 seconds
        },
        queue: [],
        timer: null,

        init: function (options) {
            this.config = { ...this.config, ...options };

            if (!this.config.apiKey) {
                console.error('HealthMonitor: API Key is required.');
                return;
            }

            this.setupGlobalHandlers();
            this.setupNetworkInterceptors();
            this.startFlushTimer();

            console.log('HealthMonitor: Initialized');
        },

        setupGlobalHandlers: function () {
            // Capture runtime errors
            window.addEventListener('error', (event) => {
                this.capture({
                    type: 'js_runtime',
                    message: event.message,
                    file: event.filename,
                    line: event.lineno,
                    column: event.colno,
                    stack: event.error ? event.error.stack : null,
                    context: {
                        url: window.location.href,
                        userAgent: navigator.userAgent
                    }
                });
            });

            // Capture unhandled promise rejections
            window.addEventListener('unhandledrejection', (event) => {
                this.capture({
                    type: 'js_promise',
                    message: event.reason ? (event.reason.message || event.reason) : 'Unhandled Promise Rejection',
                    stack: event.reason ? event.reason.stack : null,
                    context: {
                        url: window.location.href
                    }
                });
            });
        },

        setupNetworkInterceptors: function () {
            // Intercept Fetch
            const originalFetch = window.fetch;
            window.fetch = async (...args) => {
                const startTime = Date.now();
                try {
                    const response = await originalFetch(...args);
                    if (!response.ok) {
                        this.capture({
                            type: 'network_error',
                            message: `HTTP ${response.status} ${response.statusText}`,
                            context: {
                                url: args[0],
                                method: (args[1]?.method || 'GET').toUpperCase(),
                                status: response.status,
                                duration: Date.now() - startTime
                            }
                        });
                    }
                    return response;
                } catch (error) {
                    this.capture({
                        type: 'network_failure',
                        message: error.message,
                        context: {
                            url: args[0],
                            duration: Date.now() - startTime
                        }
                    });
                    throw error;
                }
            };
        },

        capture: function (errorData) {
            const payload = {
                ...errorData,
                timestamp: new Date().toISOString()
            };

            this.queue.push(payload);

            if (this.queue.length >= this.config.maxQueueSize) {
                this.flush();
            }
        },

        startFlushTimer: function () {
            if (this.timer) clearInterval(this.timer);
            this.timer = setInterval(() => {
                if (this.queue.length > 0) {
                    this.flush();
                }
            }, this.config.flushInterval);
        },

        flush: function () {
            if (this.queue.length === 0) return;

            const batch = [...this.queue];
            this.queue = []; // Clear queue immediately

            // Transform local errors to Telemetry format
            const telemetryPayload = {
                api_key: this.config.apiKey,
                checks: batch.map(err => ({
                    name: `Client Error: ${err.type}`,
                    status: 'critical', // Client errors are usually critical
                    latency: 0,
                    type: 'client_error',
                    payload: err
                }))
            };

            // Use Navigator.sendBeacon if available for reliability, or XHR/Fetch
            if (navigator.sendBeacon) {
                const blob = new Blob([JSON.stringify(telemetryPayload)], { type: 'application/json' });
                navigator.sendBeacon(this.config.endpoint, blob);
            } else {
                fetch(this.config.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(telemetryPayload),
                    keepalive: true
                }).catch(e => console.error('HealthMonitor: Failed to send report', e));
            }
        }
    };

    window.HealthMonitor = HealthMonitor;

})(window);
