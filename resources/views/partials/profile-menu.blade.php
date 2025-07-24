<button class="profile-icon" id="profile-icon">
    <i class="fa-solid fa-circle-user"></i>
</button>

<div class="profile-menu" id="profile-menu" {{ isset($menuRole) ? "role={$menuRole}" : '' }} {{ isset($menuHidden) ? "aria-hidden={$menuHidden}" : '' }}>
    @php
        $hasContent = false;
    @endphp

    @if(isset($showLogout) && $showLogout && isset($logoutLink))
        @php $hasContent = true; @endphp
        <a href="{{ $logoutLink['url'] }}">{{ $logoutLink['label'] }}</a>
    @endif

    @if(isset($customLinks) && count($customLinks) > 0)
        @php $hasContent = true; @endphp
        @foreach($customLinks as $link)
            <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
        @endforeach
    @endif

    @if(!empty(trim($slot ?? '')))
        @php $hasContent = true; @endphp
        {{ $slot }}
    @endif

    @unless($hasContent)
        <p class="profile-menu-empty">Not found</p>
    @endunless
</div>
