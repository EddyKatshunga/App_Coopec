<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf

        <!-- Numéro d'identification -->
        <div>
            <label for="numero_identification" class="block text-sm font-medium text-gray-700">Numéro d'identification</label>
            <input
                id="numero_identification"
                name="numero_identification"
                type="text"
                required
                autofocus
                autocomplete="off"
                autocorrect="off"
                autocapitalize="none"
                spellcheck="false"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            >

        </div>

        <!-- Faux champs pour que le navigateur ne remplisse jamais le formulaire -->
        <input type="text" name="fake_user" style="display:none">
        <input type="password" name="fake_pass" style="display:none">

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="off" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
