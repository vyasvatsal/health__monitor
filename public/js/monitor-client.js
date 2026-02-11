(function () {
    // 1. Get Configuration
    const script = document.currentScript;
    const apiKey = script.getAttribute('data-key');
    const endpoint = script.getAttribute('data-endpoint') || 'http://localhost:8000/api/v1/telemetry';

    if (!apiKey) {
        console.warn('AI Store Monitor: No API Key provided.');
        return;
    }

    // 2. Helper to send data
    function sendTelemetry(checks) {
        // Use sendBeacon if available for better reliability on unload
        const payload = JSON.stringify({
            api_key: apiKey,
            checks: checks
        });

        if (navigator.sendBeacon) {
            const blob = new Blob([payload], { type: 'application/json' });
            navigator.sendBeacon(endpoint, blob);
        } else {
            fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: payload,
                keepalive: true
            }).catch(e => console.error('Telemetry Error:', e));
        }
    }

    // 3. Capture Page Load Performance
    window.addEventListener('load', () => {
        // Wait a tick to ensure timing is populated
        setTimeout(() => {
            const perf = window.performance;
            if (perf && perf.timing) {
                const t = perf.timing;
                const loadTime = t.loadEventEnd - t.navigationStart;
                const dnsTime = t.domainLookupEnd - t.domainLookupStart;
                const serverTime = t.responseStart - t.requestStart;

                sendTelemetry([
                    {
                        name: 'Page Load Performance',
                        type: 'browser_performance',
                        status: loadTime > 2000 ? 'warning' : 'ok', // Simple threshold
                        latency: loadTime,
                        payload: {
                            dns: dnsTime,
                            ttfb: serverTime,
                            url: window.location.pathname
                        }
                    }
                ]);
            }
        }, 0);
    });

    // 4. Capture JS Errors
    window.addEventListener('error', (event) => {
        sendTelemetry([
            {
                name: 'JavaScript Error',
                type: 'browser_error',
                status: 'critical',
                latency: 0,
                payload: {
                    message: event.message,
                    filename: event.filename,
                    lineno: event.lineno,
                    colno: event.colno,
                    url: window.location.href
                }
            }
        ]);
    });

    console.log('AI Store Monitor: Active');
})();
