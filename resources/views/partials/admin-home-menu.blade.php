@auth
    @if(auth()->user()->isAdmin())
        <nav class="bg-emerald-950 text-white border-b border-emerald-800 shadow-sm" aria-label="Admin menü">
            <div class="max-w-5xl mx-auto px-4 py-2.5 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">
                <span class="font-semibold text-emerald-300 uppercase tracking-wide text-xs">Admin</span>
                @include('partials.admin-menu-links')
            </div>
        </nav>
    @endif
@endauth
