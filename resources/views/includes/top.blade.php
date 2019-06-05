<i id="burger" onClick="toggleMobileMenu()" class="fas fa-bars burgerMenu"></i>

<div id="piqlTop">
    <div id="piqlLogo">
        <div class="img">
            <a href="/"><img src="{{ asset('/images/piql-connect.png') }} "></a>
        </div>
    </div>
 <img id="customerLogo" src="{{ asset('/images/ikaKongsberg.svg') }}">
 <div id="piqlNav">
        <ul>
            <a href="{{ route('dashboard') }}"><li class="top-active">Home</li></a>
                <a href="{{ route('upload') }}"><li>Ingest</li></a>
                <a href="{{ route('retrieve') }}"><li>Access</li></a>
        </ul>
    </div>
    <div id="piqlOptions">
    <notifications>
    </notifications>
        <a href="settings"><i class="fas fa-cogs"></i></a>
        <i class="fas fa-info-circle"></i>
    </div>
</div>


