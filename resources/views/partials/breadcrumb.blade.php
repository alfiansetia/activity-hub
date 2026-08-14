{{-- 
    Breadcrumb Component
    Usage: @include('partials.breadcrumb', ['breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Activities', 'url' => route('activities.index')],
        ['label' => 'Activity Title'],
    ]])
--}}
<nav aria-label="breadcrumb" class="breadcrumb-nav fade-up mb-4">
    <ol class="breadcrumb mb-0">
        @foreach ($breadcrumbs as $index => $crumb)
            @if ($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    @if (isset($crumb['icon']))
                        <i class="{{ $crumb['icon'] }} me-1"></i>
                    @endif
                    {{ $crumb['label'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $crumb['url'] ?? '#' }}">
                        @if (isset($crumb['icon']))
                            <i class="{{ $crumb['icon'] }} me-1"></i>
                        @endif
                        {{ $crumb['label'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
