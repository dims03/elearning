@if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
    <button
        type="button"
        x-data="{}"
        x-cloak
        x-on:click="$dispatch('theme-changed', $store.theme === 'dark' ? 'light' : 'dark')"
        x-bind:aria-label="$store.theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
        x-bind:title="$store.theme === 'dark' ? 'Light mode' : 'Dark mode'"
        x-bind:class="{ 'is-dark': $store.theme === 'dark' }"
        class="fi-topbar-theme-toggle"
    >
        <span aria-hidden="true" class="fi-topbar-theme-toggle-track"></span>
        <span aria-hidden="true" class="fi-topbar-theme-toggle-thumb"></span>
        <span class="fi-sr-only" x-text="$store.theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"></span>
    </button>
@endif