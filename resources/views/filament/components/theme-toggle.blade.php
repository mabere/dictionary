<style>
    .fi-dropdown-panel .fi-theme-switcher { display: none !important; }
    .fi-topbar-theme-toggle { display: flex; align-items: center; margin-inline-start: .5rem; padding-inline-end: .75rem; }
</style>

<div class="fi-topbar-theme-toggle">
    <button
        type="button"
        x-data="{ theme: 'light' }"
        x-init="theme = $store.theme; $watch('theme', value => $dispatch('theme-changed', value))"
        x-on:click="theme = theme === 'dark' ? 'light' : 'dark'"
        x-bind:aria-label="theme === 'dark' ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
        x-bind:title="theme === 'dark' ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
        class="fi-icon-btn fi-size-md fi-color-gray"
    >
        <x-filament::icon icon="heroicon-o-sun" x-show="$store.theme === 'dark'" />
        <x-filament::icon icon="heroicon-o-moon" x-show="$store.theme !== 'dark'" />
    </button>
</div>
