# AutoDoc Nigeria — Basic Triage Prototype

A small client-side HTML/CSS/JavaScript demonstration for the TriTech project.

## Run locally
1. Download and extract the ZIP file.
2. Open the `autodoc_nigeria_prototype` folder.
3. Double-click `index.html` to open it in a modern browser.
4. Enter sample symptoms and/or select warning signs, then click **Show demo triage result**.

You can also open the folder in Visual Studio Code and use a local preview extension if installed.

## Features
- Symptom text input
- Green, Yellow, Red demo results
- Explicit emergency warning-sign checkboxes
- Basic English and Nigerian Pidgin guidance
- No backend or database; input stays in the browser page

## Important limitations
This is a demonstration only, not a validated medical device or clinical triage tool. The rules are simple keyword checks and can miss emergencies or misclassify symptoms. A GREEN result does not mean the person is safe. Do not use this prototype to make real medical decisions. Have qualified clinicians review and validate all rules and wording before any real-world deployment. Add appropriate emergency contact information and privacy/security review before deployment.

## Suggested demo checks
1. Select “Severe difficulty breathing” → expected RED.
2. Enter “I have fever and headache” → demo returns YELLOW.
3. Enter “Mild headache after studying” → demo returns GREEN, but this is not a clinical assurance.
4. Select “New confusion, collapse, or difficult to wake” → expected RED.
5. Try Nigerian Pidgin and review the wording with fluent speakers.
