<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLOSER - Terms of Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hover-scale {
            transition: all 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
        }
        .custom-checkbox {
            appearance: none;
            width: 24px;
            height: 24px;
            border: 2px solid #3498db;
            border-radius: 6px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .custom-checkbox:checked {
            background: #3498db;
            border-color: #3498db;
        }
        .custom-checkbox:checked::after {
            content: '✓';
            position: absolute;
            top: -2px;
            left: 4px;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <!-- CLOSER Header -->
    <div class="absolute top-6 left-0 right-0 text-center">
        <h1 class="text-5xl font-bold text-white mb-2">CLOSER</h1>
        <p class="text-white text-lg opacity-90">Connect. Match. Meet.</p>
    </div>

    <!-- Main Modal Container -->
    <div id="terms-modal" class="glass-effect rounded-2xl shadow-2xl max-w-4xl w-full max-h-[85vh] overflow-y-auto animate-fade-in mt-20">
        <!-- Modal Header -->
        <div class="sticky top-0 glass-effect border-b border-gray-200 p-6 rounded-t-2xl z-10">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Terms of Service & Privacy Policy</h2>
                <p class="text-gray-600">Please read and accept to continue</p>
                <p class="text-sm text-gray-500 mt-1">Last Updated: May 5, 2026</p>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
            <!-- Important Notice -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 border-l-4 border-blue-500 p-4 rounded-xl mb-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-blue-600 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 002h1a1 1 0 002-2V9a1 1 0 00-2-2H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-blue-800 mb-1">IMPORTANT NOTICE</h3>
                        <p class="text-blue-700 text-sm">
                            By using CLOSER, you agree to these terms and acknowledge that you have read, understood, and agree to be bound by them.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Summary Section -->
            <div class="bg-white rounded-xl p-5 mb-6 border border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2v2a1 1 0 002 2h2a1 1 0 002-2V4a1 1 0 00-2-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586A1 1 0 0013 5H4z" clip-rule="evenodd"/>
                    </svg>
                    Quick Summary
                </h3>
                
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-3 rounded-lg">
                        <h4 class="font-semibold text-blue-900 mb-1 text-sm">🌍 Geographic Focus</h4>
                        <p class="text-blue-700 text-xs">Southeast Asia & Middle East users only</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-3 rounded-lg">
                        <h4 class="font-semibold text-purple-900 mb-1 text-sm">⚖️ Legal Framework</h4>
                        <p class="text-purple-700 text-xs">Wyoming & Delaware jurisdiction</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-3 rounded-lg">
                        <h4 class="font-semibold text-green-900 mb-1 text-sm">🔐 Security</h4>
                        <p class="text-green-700 text-xs">2FA authentication & content moderation</p>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-3 rounded-lg">
                        <h4 class="font-semibold text-yellow-900 mb-1 text-sm">🛡️ Content Policy</h4>
                        <p class="text-yellow-700 text-xs">Removal without notice for reports</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-2 text-sm">📋 Key Points</h4>
                    <ul class="space-y-2 text-gray-700 text-sm">
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">✓</span>
                            <span><strong>Age Requirement:</strong> 18+ only</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">✓</span>
                            <span><strong>VPN Policy:</strong> Access from unauthorized regions may result in termination</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">✓</span>
                            <span><strong>Content Removal:</strong> We can remove content without prior notice based on reports</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">✓</span>
                            <span><strong>Data Protection:</strong> GDPR & PDPA compliant</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">✓</span>
                            <span><strong>Liability:</strong> Users are responsible for their interactions and content</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Full Terms Link -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-l-4 border-indigo-500 p-4 rounded-xl mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <h4 class="text-lg font-semibold text-indigo-900 mb-1">📄 Complete Terms Available</h4>
                        <p class="text-indigo-700 text-xs">
                            Read the full Terms of Service and Privacy Policy for detailed information
                        </p>
                    </div>
                    <a href="/terms-full" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors hover-scale inline-flex items-center text-sm whitespace-nowrap">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M11 3a1 1 0 100-2H1a1 1 0 100 2h10zm4 0a1 1 0 100-2H8a1 1 0 100 2h7z"/>
                            <path fill-rule="evenodd" d="M3 7a1 1 0 00-1 1v7a1 1 0 102 0V8a1 1 0 00-1-1zm6-4a1 1 0 00-1 1v11a1 1 0 102 0V4a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Read Full Terms
                    </a>
                </div>
            </div>

            <!-- Acceptance Checkbox and Buttons -->
            <div class="bg-white rounded-xl p-5 border border-gray-200">
                <form id="termsForm" class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" id="acceptTerms" class="custom-checkbox mt-1" required>
                        <label for="acceptTerms" class="text-gray-700 cursor-pointer select-none flex-1">
                            <strong class="text-base">I have read and agree to the Terms of Service and Privacy Policy</strong>
                            <p class="text-xs text-gray-600 mt-1">
                                By checking this box, you confirm that you have read, understood, and agree to be bound by the CLOSER Terms of Service and Privacy Policy, including the content removal policy and geographic restrictions.
                            </p>
                        </label>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-3 px-6 rounded-xl transition-all hover-scale shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Accept & Continue
                        </button>
                        <button type="button" onclick="declineTerms()" class="flex-1 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-bold py-3 px-6 rounded-xl transition-all hover-scale shadow-lg flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            Decline
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="glass-effect border-t border-gray-200 p-4 rounded-b-2xl">
            <div class="text-center">
                <p class="text-gray-600 text-sm mb-1">
                    Need help? Contact us at <a href="mailto:support@closer.com" class="text-blue-600 hover:text-blue-800 font-semibold">support@closer.com</a>
                </p>
                <p class="text-gray-500 text-xs">
                    © 2026 CLOSER. All rights reserved. | 
                    <a href="/privacy" class="text-blue-600 hover:text-blue-800">Privacy Policy</a> | 
                    <a href="/security" class="text-blue-600 hover:text-blue-800">Security Policy</a>
                </p>
            </div>
        </div>
    </div>

    <!-- JavaScript for Terms Acceptance -->
    <script>
        function acceptTerms() {
            const checkbox = document.getElementById('acceptTerms');
            
            if (!checkbox.checked) {
                alert('Please check the box to accept the terms before continuing.');
                return false;
            }

            // Store acceptance in localStorage
            localStorage.setItem('termsAccepted', 'true');
            localStorage.setItem('termsAcceptedDate', new Date().toISOString());
            
            // Set cookie for server-side verification
            document.cookie = 'terms_accepted=true; path=/; max-age=31536000; SameSite=Lax';
            
            // Redirect to intended destination or login
            const urlParams = new URLSearchParams(window.location.search);
            const redirect = urlParams.get('redirect') || '/login';
            window.location.href = redirect;
        }

        function declineTerms() {
            if (confirm('Are you sure you want to decline? You will be redirected away from CLOSER.')) {
                window.location.href = 'https://www.google.com';
            }
        }

        // Check if user already accepted terms
        window.onload = function() {
            if (localStorage.getItem('termsAccepted') === 'true') {
                const urlParams = new URLSearchParams(window.location.search);
                const redirect = urlParams.get('redirect') || '/login';
                window.location.href = redirect;
            }
        };

        // Form submission handler
        document.getElementById('termsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            acceptTerms();
        });
    </script>
</body>
</html>
                        <li>Interactions with other users</li>
                        <li>Verification of user identities and information</li>
                        <li>Compliance with local laws and regulations</li>
                        <li>Activities on social media platforms and external websites</li>
                    </ul>
                </div>

                <!-- Privacy -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">5. Privacy and Data Protection</h3>
                    <p class="text-gray-700 mb-3">We collect and process data in accordance with:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
                        <li>GDPR for European users</li>
                        <li>PDPA for Southeast Asian users</li>
                        <li>Personal Data Protection Laws for Middle Eastern users</li>
                        <li>ISO 27001 information security standards</li>
                    </ul>
                </div>

                <!-- Prohibited Activities -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">6. Prohibited Activities</h3>
                    <p class="text-gray-700 mb-3">Users are strictly prohibited from:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
                        <li>Using the service for illegal activities or fraud</li>
                        <li>Impersonating other individuals or entities</li>
                        <li>Distributing malware, viruses, or harmful code</li>
                        <li>Spamming or sending unsolicited communications</li>
                        <li>Violating privacy or rights of other users</li>
                        <li>Attempting to gain unauthorized access to the system</li>
                        <li>Using automated tools or bots without permission</li>
                        <li>Sharing explicit, offensive, or inappropriate content</li>
                    </ul>
                </div>

                <!-- Account Termination -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">7. Account Termination</h3>
                    <p class="text-gray-700 mb-3">Closer reserves the right to terminate or suspend user accounts for:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 ml-4">
                        <li>Violation of these terms of service</li>
                        <li>Suspicious or fraudulent activity</li>
                        <li>Inactivity for extended periods (12+ months)</li>
                        <li>Requests from law enforcement or regulatory authorities</li>
                        <li>Use of VPN or location masking from unauthorized regions</li>
                    </ul>
                </div>
            </div>

            <!-- Footer -->
            <div class="sticky bottom-0 bg-white border-t border-gray-200 p-6">
                <div class="flex justify-center space-x-4">
                    <button 
                        onclick="acceptTerms()" 
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors"
                    >
                        I Accept Terms of Service
                    </button>
                    <button 
                        onclick="declineTerms()" 
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors"
                    >
                        I Decline
                    </button>
                </div>
                <p class="text-center text-sm text-gray-600 mt-4">
                    By clicking "I Accept", you agree to be bound by these Terms of Service.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function acceptTerms() {
    // Store acceptance in localStorage
    localStorage.setItem('termsAccepted', 'true');
    localStorage.setItem('termsAcceptedDate', new Date().toISOString());
    
    // Hide modal
    document.getElementById('terms-modal').classList.add('hidden');
    
    // Set cookie for server-side verification
    document.cookie = 'terms_accepted=true; path=/; max-age=31536000; SameSite=Lax';
    
    // Redirect to intended destination or login
    const intendedUrl = new URLSearchParams(window.location.search).get('redirect') || '/login';
    window.location.href = intendedUrl;
}

function declineTerms() {
    // Redirect away from app
    window.location.href = 'https://www.google.com';
}

// Check if user already accepted terms
window.onload = function() {
    if (localStorage.getItem('termsAccepted') === 'true') {
        // User already accepted, hide modal and continue
        document.getElementById('terms-modal').classList.add('hidden');
        
        // Check if there's a redirect parameter
        const redirect = new URLSearchParams(window.location.search).get('redirect');
        if (redirect) {
            window.location.href = redirect;
        }
    } else {
        // Show modal
        document.getElementById('terms-modal').classList.remove('hidden');
    }
};
</script>
