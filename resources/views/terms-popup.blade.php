<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLOSER - Terms of Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen p-4">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b">
            <h1 class="text-2xl font-bold text-slate-900">CLOSER Terms of Service</h1>
            <p class="text-sm text-slate-600 mt-1">Last Updated: May 5, 2026</p>
        </div>

        <div class="p-6 space-y-6 text-slate-700">
            <section>
                <h2 class="font-semibold text-slate-900 mb-2">Data Collection for Security and Audit</h2>
                <p>
                    We collect and process security-related technical data, including your IP address and approximate geolocation
                    inferred from IP intelligence providers. This data is used only for account security, fraud prevention,
                    abuse prevention, regulatory compliance, and internal audit logging.
                </p>
                <p class="mt-2">
                    We do <strong>not</strong> sell your personal data, IP data, or geolocation data to third parties.
                </p>
            </section>

            <section>
                <h2 class="font-semibold text-slate-900 mb-2">Changes to These Terms</h2>
                <p>
                    We may update these Terms at any time, with or without prior notice. If a new acceptance is required,
                    we may request your acceptance again inside the app and/or by email before you continue using the service.
                </p>
            </section>

            <section>
                <h2 class="font-semibold text-slate-900 mb-2">Full Terms</h2>
                <p>
                    You can read the complete Terms of Service here:
                    <a href="/terms.html" target="_blank" class="text-blue-600 underline">Open full Terms document</a>.
                </p>
            </section>

            <form id="termsForm" class="space-y-4 pt-2 border-t">
                <label class="flex items-start gap-3">
                    <input id="acceptTerms" type="checkbox" class="mt-1" required>
                    <span>I have read and agree to the CLOSER Terms of Service.</span>
                </label>
                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Accept and Continue
                    </button>
                    <button type="button" id="declineBtn" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">
                        Decline
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const TERMS_VERSION = '2026-05-05';

        function saveAcceptance() {
            localStorage.setItem('termsAccepted', 'true');
            localStorage.setItem('acceptedTerms', 'true');
            localStorage.setItem('acceptedTermsVersion', TERMS_VERSION);
            localStorage.setItem('termsAcceptedDate', new Date().toISOString());

            document.cookie = 'terms_accepted=true; path=/; max-age=31536000; SameSite=Lax';
            document.cookie = `terms_accepted_version=${TERMS_VERSION}; path=/; max-age=31536000; SameSite=Lax`;
        }

        document.getElementById('termsForm').addEventListener('submit', function (event) {
            event.preventDefault();
            saveAcceptance();

            const redirect = new URLSearchParams(window.location.search).get('redirect') || '/login';
            window.location.href = redirect;
        });

        document.getElementById('declineBtn').addEventListener('click', function () {
            window.location.href = 'https://www.google.com';
        });
    </script>
</body>
</html>
