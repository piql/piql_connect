<i id="burger" onClick="toggleMobileMenu()" class="fas fa-bars burgerMenu"></i>

<div id="piqlTop">
    <div id="piqlLogo">
        <div class="img">
            <a href="/"><img src="{{ asset('/images/piql-connect.png') }} "></a>
        </div>
    </div>
 <img id="customerLogo" src="{{ asset('/images/customer_top_logo.png') }}">
 <div id="piqlNav">
        <ul>
            <a href="{{ route('dashboard') }}"><li class=" {{ \Request::is('/*') ? 'top-active' : ''  }}" >{{__('Home')}}</li></a>
            <a href="{{ route('upload') }}"><li class=" {{ \Request::is('ingest/*') ? 'top-active' : ''  }} ">{{__('Ingest') }}</li></a>
            <a href="{{ route('access.browse') }}"><li class=" {{ \Request::is('access/*') ? 'top-active' : '' }} " >{{__('Access') }}</li></a>
        </ul>
    </div>
    <div id="piqlOptions">
        <notifications>
        </notifications>
        <a href="settings"><i class="fas fa-cogs"></i></a>
        <a href="#"><i class="fas fa-info-circle" style="visibility: collapse;"></i></a>
        <a href="/logout"><i class="fas fa-sign-out-alt"></i></a>
    </div>
</div>


