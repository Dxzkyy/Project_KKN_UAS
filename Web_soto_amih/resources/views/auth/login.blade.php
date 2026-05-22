<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Soto Amih</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased bg-[#F5F0E1]">
    <div class="min-h-screen flex">

        {{-- Kiri: Logo --}}
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center bg-[#F5F0E1]">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Soto Amih" class="w-3/4 max-w-md drop-shadow-md">
        </div>

        {{-- Kanan: Form Login --}}
        <div
            class="w-full lg:w-1/2 bg-white rounded-none lg:rounded-l-[3rem] shadow-2xl flex flex-col justify-center items-center p-8 sm:p-12">
            <div class="w-full max-w-sm">
                <h2 class="text-4xl font-bold text-[#D4813B] text-center mb-10">Login</h2>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-6 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-1 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                        </div>
                        <input id="email"
                            class="block w-full pl-8 pr-3 py-2 border-0 border-b-2 border-gray-300 focus:border-[#D4813B] focus:ring-0 text-sm bg-transparent transition-colors"
                            type="email" name="email" value="{{ old('email') }}" required autofocus
                            autocomplete="username" placeholder="Email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs" />
                    </div>

                    {{-- Password --}}
                    <div class="mb-3 relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-1 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input id="password"
                            class="block w-full pl-8 pr-10 py-2 border-0 border-b-2 border-gray-300 focus:border-[#D4813B] focus:ring-0 text-sm bg-transparent transition-colors"
                            type="password" name="password" required autocomplete="current-password"
                            placeholder="Password" />

                        {{-- Toggle Password --}}
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 cursor-pointer"
                            onclick="togglePassword()">
                            <svg id="eye-icon" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <svg id="eye-off-icon" class="w-5 h-5 text-gray-400 hover:text-gray-600 hidden"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M3 3l18 18">
                                </path>
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs" />
                    </div>

                    {{-- Lupa Password --}}
                    <div class="flex justify-end mb-8">
                        @if (Route::has('password.request'))
                            <a class="text-sm text-[#D4813B] hover:text-orange-700 hover:underline transition-colors"
                                href="{{ route('password.request') }}">
                                Lupa Password ?
                            </a>
                        @endif
                    </div>

                    {{-- Tombol Login --}}
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 rounded-lg shadow-md text-base font-bold text-white bg-[#D4813B] hover:bg-[#b86d30] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#D4813B] transition duration-200 ease-in-out">
                        Login
                    </button>

                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }

        document.querySelector('button[type="submit"]').addEventListener('click', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) return;

            e.preventDefault();

            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang memverifikasi akun Anda',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            setTimeout(() => {
                document.querySelector('form').submit();
            }, 1500);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
