<style>
    .fi-sidebar-header .brand-logo-img { height: 2rem !important; }
    .fi-sidebar-header .brand-logo-text { font-size: 0.875rem !important; }
    .fi-simple-header .brand-logo-img { height: 4rem !important; }
    .fi-simple-header .brand-logo-text { font-size: 1.25rem !important; }
</style>

<div style="display: flex; align-items: center; gap: 0.5rem;">
    <img
        src="{{ asset('images/logo.png') }}"
        alt="{{ config('app.name') }}"
        class="brand-logo-img"
    >
    <span class="brand-logo-text" style="font-weight: 700; color: #F59E0B;">
        {{ config('app.name') }}
    </span>
</div>
