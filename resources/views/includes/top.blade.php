<div class="piqlTop">
    <nav class="navbar navbar-expand-xl navbar-expand-sm navbar-light"> <!-- piqlNav "-->

        <div class="col-sm-3 mr-3">
            <a class="navbar-brand" href="/"><img class="w-250 mt-2em" src="{{ asset('/images/piql-connect.png') }} "></a>
        </div>

        <div class="col-sm-2">
            <img class="navbar-brand h-80 mt-2em ml-5" src="{{ asset('/images/customer_top_logo.png') }}">
        </div>

        <div class="col-sm-5 ml-auto mr-1">
            <ul class="navbar-nav ul-auto pt-5 ml-auto signal">
                <li class="navbar mr-3 {{ \Request::is('/*') ? 'top-active' : 'top-inactive'  }}" ><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
                <li class="navbar mr-3 {{ \Request::is('ingest/*') ? 'top-active' : 'top-inactive'  }} "><a href="{{ route('upload') }}">{{__('Ingest') }}</a></li>
                <li class="navbar mr-3 {{ \Request::is('access/*') ? 'top-active' : 'top-inactive' }} " ><a href="{{ route('access.browse') }}">{{__('Access') }}</a></li>
            @if (false) /*TODO: Auth check*/
                <li class="navbar mr-5 {{ \Request::is('planning/*') ? 'top-active' : 'top-inactive' }} " ><a href="{{ route('planning.archives') }}">{{__('Planning') }}</a></li>
            @endif
                <li class="navbar mr-3"><settings-dropdown></settings-dropdown></li>
                <li class="navbar mr-3"><notifications></notifications></li>
                <li class="navbar mr-4"><a href="/logout"><i class="fas fa-sign-out-alt signal"></i></a></li>
            </ul>
        </div>
    </nav>
</div>
