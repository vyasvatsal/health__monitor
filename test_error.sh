curl -X POST http://127.0.0.1:8000/api/v1/monitor/1/capture \
    -H "Content-Type: application/json" \
    -d '{"message": "Test Error via Curl", "stack_trace": "Error: Test\n    at <anonymous>:1:1", "fingerprint": "test-error-1"}'
