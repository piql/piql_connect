<div class="piqlTop">
    <nav class="mb-3 mt-3 navbar-expand-xl navbar-expand-sm navbar-light">
        <div class="row">
        <div class="col-md-4 ml-5 mr-0 align-self-center pr-0" style="margin-right: 11rem !important">
            <a class="navbar-brand pr-5" href="/"><img class="" style="width: 15rem;" src="{{ asset('/images/piql-connect.png') }} "></a>
            <img class="h-80 text-right ml-4" src="{{ asset('/images/customer_top_logo.png') }}">
        </div>

        <div class="col"></div>

        <div class="col navLinks mr-5">
            <ul class="navbar-nav ul-auto signal">
                <li class="pr-5 navbar {{ \Request::is('/*') ? 'top-active' : 'top-inactive'  }}" ><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
                <li class="pr-5 navbar {{ \Request::is('ingest/*') ? 'top-active' : 'top-inactive'  }} "><a href="{{ route('upload') }}">{{__('Ingest') }}</a></li>
                <li class="pr-5 navbar {{ \Request::is('access/*') ? 'top-active' : 'top-inactive' }} " ><a href="{{ route('access.browse') }}">{{__('Access') }}</a></li>
            @if (false) /*TODO: Auth check*/
                <li class="pr-5 navbar {{ \Request::is('planning/*') ? 'top-active' : 'top-inactive' }} " ><a href="{{ route('planning.archives') }}">{{__('Planning') }}</a></li>
            @endif
                <li class="pr-3 plistIcon navbar"><settings-dropdown></settings-dropdown></li>
                <li class="pr-3 plistIcon navbar"><notifications></notifications></li>
                <li class="pr-3 plistIcon navbar"><a href="/logout"><i class="fas fa-sign-out-alt signal"></i></a></li>
            </ul>
        </div>
</div>
    </nav>
</div>
