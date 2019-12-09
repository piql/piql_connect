<div class="piqlTop">
    <nav class="navbar navbar-expand-xl navbar-expand-sm navbar-light">

        <div class="col-md-3 mr-5 align-self-center pr-5">
            <a class="navbar-brand" href="/"><img class="w-250 mt-2em" src="{{ asset('/images/piql-connect.png') }} "></a>
        </div>

        <div class="col-md-2 mr-4 ml-1 pl-0">
            <img class="navbar-brand h-80" src="{{ asset('/images/customer_top_logo.png') }}">
        </div>

        <div class="col"></div>

        <div class="col w-auto mt-5">
            <ul class="navbar-nav ul-auto signal">
                <li class="pr-5 navbar {{ \Request::is('/*') ? 'top-active' : 'top-inactive'  }}" ><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
                <li class="pr-5 navbar {{ \Request::is('ingest/*') ? 'top-active' : 'top-inactive'  }} "><a href="{{ route('upload') }}">{{__('Ingest') }}</a></li>
                <li class="pr-5 navbar {{ \Request::is('access/*') ? 'top-active' : 'top-inactive' }} " ><a href="{{ route('access.browse') }}">{{__('Access') }}</a></li>
            @if (false) /*TODO: Auth check*/
                <li class="pr-5 navbar {{ \Request::is('planning/*') ? 'top-active' : 'top-inactive' }} " ><a href="{{ route('planning.archives') }}">{{__('Planning') }}</a></li>
            @endif
                <li class="pr-3 navbar"><settings-dropdown></settings-dropdown></li>
                <li class="pr-3 navbar"><notifications></notifications></li>
                <li class="pr-3 navbar"><a href="/logout"><i class="fas fa-sign-out-alt signal"></i></a></li>
            </ul>
        </div>
    </nav>
</div>
