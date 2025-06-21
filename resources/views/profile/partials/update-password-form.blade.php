<section class="space-y-6">
    <header class="pb-4 border-b border-blue-200">
        <h2 class="text-xl font-semibold text-blue-900 flex items-center">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2m0 0V3a2 2 0 00-2-2m2 2a2 2 0 002 2m0 0v2a2 2 0 002 2m-2-2a2 2 0 00-2-2"></path>
                </svg>
            </div>
            Update Password
        </h2>
        <p class="mt-2 text-sm text-blue-700 ml-13">
            Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.
        </p>
    </header>

    {{-- Password Security Tips --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="font-medium text-blue-900 mb-2 flex items-center">
            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            Tips Password Aman
        </h4>
        <ul class="text-sm text-blue-800 space-y-1">
            <li class="flex items-start">
                <svg class="w-3 h-3 mt-1 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Minimal 8 karakter
            </li>
            <li class="flex items-start">
                <svg class="w-3 h-3 mt-1 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Kombinasi huruf besar, kecil, angka dan simbol
            </li>
            <li class="flex items-start">
                <svg class="w-3 h-3 mt-1 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Hindari informasi pribadi (nama, tanggal lahir, dll)
            </li>
        </ul>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        {{-- Current Password --}}
        <div class="space-y-2">
            <label for="update_password_current_password" class="block text-sm font-medium text-blue-800">
                <svg class="w-4 h-4 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Password Saat Ini
            </label>
            <div class="relative">
                <input id="update_password_current_password" 
                       name="current_password" 
                       type="password" 
                       class="w-full px-4 py-3 pr-12 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-blue-400" 
                       autocomplete="current-password"
                       placeholder="Masukkan password saat ini">
                <button type="button" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password"
                        data-target="update_password_current_password">
                    <svg class="w-5 h-5 text-blue-400 hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            @error('current_password', 'updatePassword')
                <p class="text-red-500 text-xs mt-1 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- New Password --}}
        <div class="space-y-2">
            <label for="update_password_password" class="block text-sm font-medium text-blue-800">
                <svg class="w-4 h-4 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2m0 0V3a2 2 0 00-2-2m2 2a2 2 0 002 2m0 0v2a2 2 0 002 2m-2-2a2 2 0 00-2-2"></path>
                </svg>
                Password Baru
            </label>
            <div class="relative">
                <input id="update_password_password" 
                       name="password" 
                       type="password" 
                       class="w-full px-4 py-3 pr-12 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-blue-400" 
                       autocomplete="new-password"
                       placeholder="Masukkan password baru">
                <button type="button" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password"
                        data-target="update_password_password">
                    <svg class="w-5 h-5 text-blue-400 hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            @error('password', 'updatePassword')
                <p class="text-red-500 text-xs mt-1 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
            
            {{-- Password Strength Indicator --}}
            <div class="password-strength-container hidden">
                <div class="flex items-center space-x-2 mt-2">
                    <span class="text-xs text-blue-600 font-medium">Kekuatan Password:</span>
                    <div class="flex space-x-1">
                        <div class="password-strength-bar w-4 h-2 bg-gray-200 rounded-full"></div>
                        <div class="password-strength-bar w-4 h-2 bg-gray-200 rounded-full"></div>
                        <div class="password-strength-bar w-4 h-2 bg-gray-200 rounded-full"></div>
                        <div class="password-strength-bar w-4 h-2 bg-gray-200 rounded-full"></div>
                    </div>
                    <span class="password-strength-text text-xs text-gray-500"></span>
                </div>
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="space-y-2">
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-blue-800">
                <svg class="w-4 h-4 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Konfirmasi Password Baru
            </label>
            <div class="relative">
                <input id="update_password_password_confirmation" 
                       name="password_confirmation" 
                       type="password" 
                       class="w-full px-4 py-3 pr-12 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-blue-400" 
                       autocomplete="new-password"
                       placeholder="Konfirmasi password baru">
                <button type="button" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password"
                        data-target="update_password_password_confirmation">
                    <svg class="w-5 h-5 text-blue-400 hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </button>
            </div>
            @error('password_confirmation', 'updatePassword')
                <p class="text-red-500 text-xs mt-1 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between pt-6 border-t border-blue-200">
            @if (session('status') === 'password-updated')
                <div class="flex items-center text-green-600 bg-green-50 px-3 py-2 rounded-lg border border-green-200" 
                     x-data="{ show: true }" 
                     x-show="show" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     x-init="setTimeout(() => show = false, 4000)">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium">Password berhasil diperbarui!</span>
                </div>
            @else
                <div></div>
            @endif

            <div class="flex space-x-3">
                <button type="button" 
                        onclick="document.getElementById('update_password_current_password').value=''; document.getElementById('update_password_password').value=''; document.getElementById('update_password_password_confirmation').value='';"
                        class="px-4 py-2 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 focus:ring-2 focus:ring-blue-300 transition-all duration-200 font-medium">
                    Reset Form
                </button>
                <button type="submit" 
                        class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 transition-all duration-200 flex items-center shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Update Password
                </button>
            </div>
        </div>
    </form>

    {{-- Security Info Card --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 mt-6">
        <div class="flex items-start">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-medium text-blue-900 mb-1">Informasi Keamanan</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Password akan di-enkripsi secara otomatis</li>
                    <li>• Jangan bagikan password Anda kepada siapa pun</li>
                    <li>• Logout dari semua perangkat setelah mengubah password</li>
                    <li>• Pertimbangkan menggunakan password manager</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('svg');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        });
    });

    // Password strength indicator
    const newPasswordInput = document.getElementById('update_password_password');
    const strengthContainer = document.querySelector('.password-strength-container');
    const strengthBars = document.querySelectorAll('.password-strength-bar');
    const strengthText = document.querySelector('.password-strength-text');

    newPasswordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        if (password.length > 0) {
            strengthContainer.classList.remove('hidden');
            updateStrengthIndicator(strength);
        } else {
            strengthContainer.classList.add('hidden');
        }
    });

    function calculatePasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        
        return score;
    }

    function updateStrengthIndicator(strength) {
        const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
        const texts = ['Sangat Lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
        
        strengthBars.forEach((bar, index) => {
            bar.className = 'password-strength-bar w-4 h-2 rounded-full transition-colors duration-200';
            if (index < strength) {
                bar.classList.add(colors[strength - 1]);
            } else {
                bar.classList.add('bg-gray-200');
            }
        });
        
        strengthText.textContent = texts[strength - 1] || '';
        strengthText.className = `password-strength-text text-xs ${strength <= 2 ? 'text-red-600' : strength <= 3 ? 'text-yellow-600' : 'text-green-600'}`;
    }

    // Add input focus effects
    const inputs = document.querySelectorAll('input[type="password"]');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.classList.add('transform', '-translate-y-0.5', 'shadow-lg');
        });
        
        input.addEventListener('blur', () => {
            input.classList.remove('transform', '-translate-y-0.5', 'shadow-lg');
        });
    });
});
</script>