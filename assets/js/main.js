/**
 * Tundra Lead Capture - Frontend Application Engine
 * Handles AJAX submission, dynamic UI states, file downloads, 
 * and asynchronous Google reCAPTCHA v3 script injection.
 */

// 1. Configuration
const RECAPTCHA_SITE_KEY = (typeof window.TUNDRA_CONFIG !== 'undefined') ? window.TUNDRA_CONFIG.recaptchaSiteKey : '';

/**
 * Dynamically injects the Google reCAPTCHA v3 script into the document head.
 * Prevents the need to manually hardcode the script tag in the HTML.
 * * @param {string} siteKey The public reCAPTCHA v3 Site Key.
 * @return {void}
 */
function loadRecaptchaScript(siteKey) {
    if (!siteKey) return;

    const script = document.createElement('script');
    script.src = `https://www.google.com/recaptcha/api.js?render=${siteKey}`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
}

/**
 * Automatically populates form hidden fields from URL parameters.
 * @return {void}
 */
function initUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const paramsToCapture = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
    const form = document.getElementById('tundraLeadForm');

    if (!form) return;

    paramsToCapture.forEach(param => {
        if (urlParams.has(param)) {
            const input = form.querySelector(`[name="${param}"]`);
            if (input) {
                input.value = urlParams.get(param);
            }
        }
    });
}

/**
 * Intercepts the Tundra Lead form submission to process via AJAX.
 * Applies loading states, handles token generation, and triggers delivery.
 * * @listens DOMContentLoaded
 */
document.addEventListener('DOMContentLoaded', () => {

    initUrlParams();
    // Initialize the reCAPTCHA API if a key is configured
    loadRecaptchaScript(RECAPTCHA_SITE_KEY);

    const form = document.getElementById('tundraLeadForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById('tundraSubmitBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoader = submitBtn.querySelector('.tundra-spinner');
        const noticeArea = document.getElementById('tundraFormNotice');

        // Lock UI & Initialize Animation
        submitBtn.classList.add('is-loading');
        btnText.textContent = 'Processing...';
        btnLoader.style.display = 'inline-block';
        noticeArea.style.display = 'none';

        /**
         * Transmits the FormData payload to the PHP endpoint.
         * * @param {string|null} token The generated reCAPTCHA token, or null if disabled.
         * @return {void}
         */
        const transmitLead = (token) => {
            const formData = new FormData(form);

            // Append the security token if generated
            if (token) {
                formData.append('g-recaptcha-response', token);
            }

            fetch('process-lead.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        // Success UI State
                        btnText.textContent = res.download_url ? 'Downloaded' : 'Submitted';
                        btnLoader.style.display = 'none';

                        noticeArea.style.display = 'block';
                        noticeArea.style.color = '#2DA1FF';
                        noticeArea.innerHTML = res.message;

                        // Initiate File Download silently if URL is provided
                        if (res.download_url) {
                            const a = document.createElement('a');
                            a.href = res.download_url;
                            a.download = '';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        }

                        form.reset();
                    } else {
                        throw new Error(res.message);
                    }
                })
                .catch(error => {
                    // Error UI State
                    submitBtn.classList.remove('is-loading');
                    btnText.textContent = 'Download Payload Spec Sheet';
                    btnLoader.style.display = 'none';

                    noticeArea.style.display = 'block';
                    noticeArea.style.color = '#ff4a4a';
                    noticeArea.textContent = error.message || 'An unexpected error occurred.';
                });
        };

        // Logic Gate: Execute reCAPTCHA if active, otherwise process form immediately
        if (RECAPTCHA_SITE_KEY && typeof grecaptcha !== 'undefined') {
            grecaptcha.ready(function () {
                grecaptcha.execute(RECAPTCHA_SITE_KEY, { action: 'submit' })
                    .then(function (token) {
                        transmitLead(token);
                    })
                    .catch(function (err) {
                        console.error('reCAPTCHA execution failed:', err);
                        transmitLead(null); // Fallback execution
                    });
            });
        } else {
            transmitLead(null);
        }
    });
});