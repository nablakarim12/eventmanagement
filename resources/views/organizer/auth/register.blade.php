<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Registration - ConVex</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">
    <!-- Left Side - Registration Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-white overflow-y-auto">
        <div class="max-w-2xl w-full space-y-8 py-12">
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('assets/images/4bgremove.png') }}" alt="ConVex" class="h-20 w-auto">
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Register as Event Organizer
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Join our platform to create and manage events
                </p>
            </div>

            <!-- Google Sign Up Button -->
            <div class="mt-6">
                <a href="{{ route('organizer.google.redirect') }}" 
                   class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Sign up with Google
                </a>

                <div class="mt-6 relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or register with email</span>
                    </div>
                </div>
            </div>
            
            <form class="mt-8 space-y-6" action="{{ route('organizer.register') }}" method="POST" enctype="multipart/form-data" id="registrationForm">
            @csrf
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Basic Registration Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Registration Information</h3>
                <p class="text-sm text-gray-500">Fields marked with * are required. Other details can be updated later in your profile.</p>
                
                <div>
                    <label for="org_name" class="block text-sm font-medium text-gray-700">Organization Name *</label>
                    <input id="org_name" name="org_name" type="text" required value="{{ old('org_name') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Enter your organization name">
                </div>
                
                <div>
                    <label for="org_email" class="block text-sm font-medium text-gray-700">Organization Email / Personal Email *</label>
                    <input id="org_email" name="org_email" type="email" required value="{{ old('org_email') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="example@organization.com">
                    <p class="mt-1 text-xs text-gray-500">This email will be used for login and notifications</p>
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                    <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="+60123456789">
                </div>
            </div>

            <!-- Supporting Documents -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Supporting Documents</h3>
                <p class="text-sm text-gray-600">Upload relevant documents to support your organization registration (e.g., business license, tax certificate, registration certificate)</p>
                
                <div>
                    <label for="documents" class="block text-sm font-medium text-gray-700">Upload Documents (PDF only, max 10MB each)</label>
                    <input id="documents" name="documents[]" type="file" multiple accept=".pdf" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">You can select multiple PDF files. These documents will be reviewed by admin for approval.</p>
                </div>
            </div>

            <!-- Password Section -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Account Security</h3>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <div class="mt-1 relative">
                        <input id="password" name="password" type="password" required 
                               class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Enter a strong password">
                        <button type="button" onclick="togglePassword('password')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="password-eye-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Password Requirements -->
                    <div class="mt-2 space-y-1">
                        <p class="text-xs font-medium text-gray-700">Password must contain:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li id="req-length" class="flex items-center">
                                <span class="mr-2">○</span> Minimum 8 characters
                            </li>
                            <li id="req-uppercase" class="flex items-center">
                                <span class="mr-2">○</span> At least one uppercase letter (A-Z)
                            </li>
                            <li id="req-lowercase" class="flex items-center">
                                <span class="mr-2">○</span> At least one lowercase letter (a-z)
                            </li>
                            <li id="req-number" class="flex items-center">
                                <span class="mr-2">○</span> At least one number (0-9)
                            </li>
                            <li id="req-special" class="flex items-center">
                                <span class="mr-2">○</span> At least one special character (@ # $ % ! & *)
                            </li>
                            <li id="req-space" class="flex items-center">
                                <span class="mr-2">○</span> No spaces allowed
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                    <div class="mt-1 relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" required 
                               class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Re-enter your password">
                        <button type="button" onclick="togglePassword('password_confirmation')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="password_confirmation-eye-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <p id="password-match-message" class="mt-1 text-xs"></p>
                </div>
            </div>

            <div>
                <button type="submit" id="submitBtn"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Register Organization
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('organizer.login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-eye-icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                field.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        // Password validation
        const passwordInput = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const submitBtn = document.getElementById('submitBtn');

        function validatePassword() {
            const password = passwordInput.value;
            
            // Check each requirement
            const requirements = {
                'req-length': password.length >= 8,
                'req-uppercase': /[A-Z]/.test(password),
                'req-lowercase': /[a-z]/.test(password),
                'req-number': /[0-9]/.test(password),
                'req-special': /[@#$%!&*]/.test(password),
                'req-space': !/\s/.test(password)
            };

            // Update UI for each requirement
            let allValid = true;
            for (const [id, isValid] of Object.entries(requirements)) {
                const element = document.getElementById(id);
                if (isValid) {
                    element.classList.add('text-green-600');
                    element.classList.remove('text-gray-600', 'text-red-600');
                    element.querySelector('span').textContent = '✓';
                } else {
                    element.classList.add('text-red-600');
                    element.classList.remove('text-gray-600', 'text-green-600');
                    element.querySelector('span').textContent = '✗';
                    allValid = false;
                }
            }

            // Check if passwords match
            checkPasswordMatch();

            return allValid;
        }

        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmation = passwordConfirmation.value;
            const messageElement = document.getElementById('password-match-message');

            if (confirmation === '') {
                messageElement.textContent = '';
                return;
            }

            if (password === confirmation) {
                messageElement.textContent = '✓ Passwords match';
                messageElement.classList.add('text-green-600');
                messageElement.classList.remove('text-red-600');
            } else {
                messageElement.textContent = '✗ Passwords do not match';
                messageElement.classList.add('text-red-600');
                messageElement.classList.remove('text-green-600');
            }
        }

        // Prevent common weak passwords
        const weakPasswords = ['password', '123456', '12345678', 'qwerty', 'abc123', 'password123'];
        
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = passwordInput.value.toLowerCase();
            
            if (weakPasswords.includes(password)) {
                e.preventDefault();
                alert('This password is too common and easily guessed. Please choose a stronger password.');
                passwordInput.focus();
                return false;
            }

            if (!validatePassword()) {
                e.preventDefault();
                alert('Please ensure your password meets all the requirements.');
                passwordInput.focus();
                return false;
            }

            if (passwordInput.value !== passwordConfirmation.value) {
                e.preventDefault();
                alert('Passwords do not match. Please check and try again.');
                passwordConfirmation.focus();
                return false;
            }
        });

        // Real-time validation
        passwordInput.addEventListener('input', validatePassword);
        passwordConfirmation.addEventListener('input', checkPasswordMatch);
    </script>
    
    <!-- Right Side - Image -->
    <div class="hidden lg:flex lg:w-1/2 items-center justify-center">
        <img src="{{ asset('assets/images/eoreg.png') }}" alt="Event Registration" class="w-full h-full object-cover">
    </div>
</body>
</html>