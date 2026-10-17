# AutoDoc Nigeria

AutoDoc Nigeria provides symptom triage guidance through a PHP endpoint backed by the Gemini API.

## Run locally
Requirements: PHP with cURL enabled, Composer, and a Gemini API key.

1. Install dependencies with `composer install`.
2. Copy `.env.example` to `.env` and set `GCP_API_KEY` to your Gemini API key.
3. Start PHP's built-in server from the project root with `php -S localhost:8000`.
4. Open `http://localhost:8000` in your browser. Do not open `index.html`
   directly or use a static preview server; the page needs the PHP endpoint
   at `api/triage.php` relative to the app URL.

Keep `.env` private and never commit your API key. If the page displays
“Unable to connect to the AI triage service,” check the PHP/Apache error log
(or the PHP server terminal) for a `Gemini API cURL error` entry. It contains
the transport error needed to distinguish a timeout, DNS, or TLS problem; do
not share your API key when reporting it. Also confirm the server can make
outbound HTTPS connections to Google's API.

## Features
- Symptom text input in English or Nigerian Pidgin
- Gemini-backed urgency assessment and first-aid guidance
- Health facility details when returned by the service
- Emergency warning and privacy reminders

## Important limitations
This is a demonstration only, not a validated medical device or clinical triage tool. The rules are simple keyword checks and can miss emergencies or misclassify symptoms. A GREEN result does not mean the person is safe. Do not use this prototype to make real medical decisions. Have qualified clinicians review and validate all rules and wording before any real-world deployment. Add appropriate emergency contact information and privacy/security review before deployment.

## Suggested demo checks
1. Select “Severe difficulty breathing” → expected RED.
2. Enter “I have fever and headache” → demo returns YELLOW.
3. Enter “Mild headache after studying” → demo returns GREEN, but this is not a clinical assurance.
4. Select “New confusion, collapse, or difficult to wake” → expected RED.
5. Try Nigerian Pidgin and review the wording with fluent speakers.
