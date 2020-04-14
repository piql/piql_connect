<div class="piqlTop">
    <nav class="mb-2 mt-3 navbar-expand-xl navbar-expand-sm navbar-light">
        <div class="row pt-3">
            <div class="col-md-3 "> 
                <a class="navbar-brand" href="/"><img class="" style="position: fixed; left: 3rem; width: 15rem;" src="{{ asset('/images/piql-connect.png') }} "></a>
            </div>
            <div class="col-md-1 ml-0 mr-0 pr-0">
                <img style="position: fixed; left: 22rem; width: 10rem;" src="{{ asset('/images/customer_top_logo.png') }}">
            </div>

            <div class="col-md-1"> 
                <session-timeout-monitor
                    :navigation-activity-time="{!! Session::get('lastActivityTime') !!}"
                    :session-lifetime-ms="{!! Session::get('sessionLifetimeMs') !!}"
                    :interval="1000"
					:no-refresh.boolean="true" />
            </div>

            <!-- div class="col-md-7 navLinks w-100">
                <ul class="navbar-nav m-auto signal">
                    <li class="navbar {{ \Request::is('/*') ? 'top-active' : 'top-inactive'  }}" ><a href="{{ route('/') }}">{{__('Home')}}</a></li>
                    <li class="navbar {{ \Request::is('ingest/*') ? 'top-active' : 'top-inactive'  }} "><a href="{{ route('/') }}">{{__('Ingest') }}</a></li>
                    <li class="navbar {{ \Request::is('access/*') ? 'top-active' : 'top-inactive' }} " ><a href="{{ route('/') }}">{{__('Access') }}</a></li>
                @if (false) /*TODO: Auth check*/
                    <li class="navbar {{ \Request::is('planning/*') ? 'top-active' : 'top-inactive' }} " ><a href="{{ route('/') }}">{{__('Planning') }}</a></li>
                @endif
                    <li class="pl-3 pr-3 plistIcon navbar"><settings-dropdown></settings-dropdown></li>
                    <li class="pr-3 plistIcon navbar"><notifications></notifications></li>
                    <li class="pr-3 plistIcon navbar"><a href="/logout"><i class="fas fa-sign-out-alt signal"></i></a></li>
                </ul>
            </div>
        </div-->
    </nav>
</div>
