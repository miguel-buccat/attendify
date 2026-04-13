import { Html5Qrcode } from 'html5-qrcode';
import axios from 'axios';

const readerEl = document.getElementById('qr-reader');
const statusEl = document.getElementById('camera-status');
const resultEl = document.getElementById('scan-result');

if (readerEl) {
    const scanner = new Html5Qrcode('qr-reader');
    let isProcessing = false;

    const showResult = (message, type) => {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
        resultEl.innerHTML = `<div class="alert ${alertClass} rounded-xl"><span>${message}</span></div>`;
        resultEl.classList.remove('hidden');
    };

    const onScanSuccess = async (decodedText) => {
        if (isProcessing) return;
        isProcessing = true;

        let payload;

        // Try parsing as URL first (new format), then JSON (legacy)
        try {
            const url = new URL(decodedText);
            const pathParts = url.pathname.split('/').filter(Boolean);
            // Expected: /attend/{session_id}/{token}
            if (pathParts.length >= 3 && pathParts[0] === 'attend') {
                payload = { session_id: pathParts[1], token: pathParts[2] };
            }
        } catch {
            // Not a URL, try JSON
        }

        if (!payload) {
            try {
                payload = JSON.parse(decodedText);
            } catch {
                showResult('Invalid QR code format.', 'error');
                isProcessing = false;
                return;
            }
        }

        if (!payload.session_id || !payload.token) {
            showResult('Invalid QR code format.', 'error');
            isProcessing = false;
            return;
        }

        try {
            await scanner.stop();
        } catch {
            // scanner may already be stopped
        }

        statusEl.textContent = 'Processing…';

        try {
            const response = await axios.post('/student/scan', {
                session_id: payload.session_id,
                token: payload.token,
            });

            const data = response.data;
            showResult(
                `Attendance recorded: <strong>${data.status}</strong> for ${data.class_name} (${data.session_time})`,
                'success'
            );
            statusEl.textContent = 'Scan complete.';
        } catch (error) {
            const message = error.response?.data?.message || 'Something went wrong. Please try again.';
            showResult(message, 'error');
            statusEl.textContent = 'You can try scanning again.';

            // Restart scanner after error (except for already-recorded)
            if (error.response?.status !== 409) {
                setTimeout(() => {
                    isProcessing = false;
                    startScanner();
                }, 3000);
            }
        }
    };

    const startScanner = () => {
        scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess,
        ).then(() => {
            statusEl.textContent = 'Point your camera at the QR code.';
        }).catch((err) => {
            statusEl.textContent = 'Camera access denied or unavailable. Please allow camera permissions.';
            console.error('QR Scanner error:', err);
        });
    };

    startScanner();
}
