# AutoDoc Nigeria

AutoDoc Nigeria is a symptom-triage prototype that provides AI-generated urgency
guidance through a PHP endpoint backed by the Gemini API.

## Run locally

Requirements: PHP with cURL enabled, Composer, and a Gemini API key.

1. Install the PHP dependencies from the project root:

   ```sh
   composer install
   ```

2. Create a `.env` file in the project root and add your API key:

   ```dotenv
   GCP_API_KEY=your-gemini-api-key
   ```

3. Start PHP's built-in web server from the project root:

   ```sh
   php -S localhost:8000
   ```

4. Open <http://localhost:8000> in your browser. Do not open `index.html`
   directly or use a static preview server; the page sends requests to the PHP
   endpoint at `api/triage.php`.

Keep `.env` private and never commit your API key. Symptom descriptions are
sent to the configured Gemini API for assessment, so do not enter names or
other identifying information. If the page cannot connect to the service,
check the PHP server or web server error log for a `Gemini API cURL error` and
confirm the server can make outbound HTTPS connections to Google's API. Do not
share your API key when reporting a problem.

## Features

- Symptom input in English or Nigerian Pidgin
- Gemini-backed GREEN, YELLOW, or RED urgency assessment
- AI-generated summaries, first-aid guidance, and warning signs
- Emergency and privacy reminders

## Important limitations

This is a demonstration only, not a validated medical device or clinical triage
tool. AI-generated guidance can be incomplete or incorrect, and a GREEN result
does not mean the person is safe. Do not use this prototype to make real
medical decisions or delay emergency care. Seek emergency medical help
immediately for life-threatening symptoms. Have qualified clinicians review
and validate all rules and wording before any real-world deployment, and
complete appropriate privacy and security reviews.
