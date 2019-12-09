<div class="sideMenu" id="sideMenu">

    <div id="welcomeText">
        {{__("Welcome")}}<br><i class="fas fa-user"></i> {{ Auth::user()->full_name }}
    </div>

    <!--div id="collapseSideMenu">
        <img onclick="collapseMenu()" src="{{asset('/images/collapsemenu.png')}}">
    </div-->

    <div class="clearboth">
    </div>

    <ul class="list-group">
    @if(\Request::is('/'))
        <a href="{{ route('dashboard') }}">
            <li class="list-group-item  {{ \Request::is('/') ? 'active sidebar-active' : '' }} ">
                <i class="fas fa-tachometer-alt"></i><i class="leftMenuItem">{{__('sidebar.dashboard')}}</i>
            </li>
        </a>
        <a href="{{ route('dashboard') }}">
            <li class="list-group-item {{ \Request::is('reports') ? 'active sidebar-active' : '' }} ">
                <i class="fas fa-chart-bar"></i><i class="leftMenuItem">{{__('sidebar.reports')}}</i>
            </li>
        </a>


    @elseif  (\Request::is('ingest/*'))
        <a href="{{ route('upload') }}">
            <li class="list-group-item {{ \Request::is('ingest/upload') ? 'active sidebar-active' : '' }} ">
                <i class="fas fa-upload"></i><i class="leftMenuItem">{{__('sidebar.upload')}}</i>
            </li>
        </a>
        <a href="{{ route('process') }}">
            <li class="list-group-item {{ \Request::is('ingest/process') ? 'active sidebar-active' : '' }} ">
                <i class="fas fa-hourglass-half"></i><i class="leftMenuItem">{{__('sidebar.processing')}}</i>
            </li></a>
        <a href="{{ route('offline_storage') }}">
            <li class="list-group-item {{ \Request::is('ingest/offline_storage') ? 'active sidebar-active' : '' }} ">
                <i class="fas fa-clock"></i><i class="leftMenuItem">{{__("sidebar.taskList")}}</i>
            </li>
        </a>
        <a href="{{ route('status') }}">
            <li class="list-group-item {{ \Request::is('ingest/status') ? 'active sidebar-active' : '' }} ">
                <i class="fas fa-clipboard-check"></i><i class="leftMenuItem">{{__('sidebar.status')}}</i>
            </li>
        </a>

    @elseif (\Request::is('access/*' || 'access/'))
        <a href="{{ route('access.browse') }}">
            <li class="list-group-item {{ \Request::is('access/browse') ? 'active sidebar-active' : '' }} ">
                <i class="fas fa-hdd"></i><i class="leftMenuItem">{{__('sidebar.browse')}}</i>
            </li>
        </a>
        <a href="{{ route('access.retrieve.ready') }}">
            <li class="list-group-item {{ \Request::is('access/retrieve/ready') ? 'active sidebar-active' : '' }} ">
                <i class="fas fa-file-export"></i><i class="leftMenuItem">{{__('sidebar.retrieve.readyToRetrieve')}}</i>
            </li>
        </a>

        <a href="{{ route('access.retrieve.retrieving') }}">
            <li class="list-group-item {{ \Request::is('access/retrieve/retrieving') ? 'active sidebar-active' : '' }} ">
                <i class="fas fa-spinner"></i><i class="leftMenuItem">{{__('sidebar.retrieve.retrieving')}}</i>
            </li>
        </a>

        <a href="{{ route('access.retrieve.downloadable') }}">
            <li class="list-group-item {{ \Request::is('access/retrieve/downloadable') ? 'active sidebar-active' : '' }} ">
                <i class="fas fa-file-download"></i><i class="leftMenuItem">{{__('sidebar.retrieve.downloadable')}}</i>
            </li>
        </a>

        <a href="{{ route('access.retrieve.history') }}">
            <li class="list-group-item {{ \Request::is('access/retrieve/history') ? 'active sidebar-active' : '' }} ">
                <i class="fas fa-history"></i><i class="leftMenuItem">{{__('sidebar.retrieve.history')}}</i>
            </li>
        </a>
@endif
    </ul>

    <div class="poweredBy">
         <div class="poweredByText">Powered by </div>
        <span class="poweredByImg"><img src="{{asset('/images/piql_logo_white.png')}}"><span>
	</div>
</div>
