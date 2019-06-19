<div class="wrapper sideMenu">
    <div id="welcomeText">
        Welcome<br><i class="fas fa-user"></i> {{ Auth::user()->full_name }}
    </div>
    <div id="collapseSideMenu">
        <img onclick="collapseMenu()" src="{{asset('/images/collapsemenu.png')}}">
    </div>
    <div class="clearboth"></div>
    <ul class="list-group">
    @if  (\Request::is('ingest/*'))
        <a href="{{ route('upload') }}"><li class="list-group-item {{ \Request::is('ingest/upload') ? 'active sidebar-active' : '' }} "><i class="fas fa-upload"></i><i class="leftMenuItem"> Upload</i></li></a>
        <a href="{{ route('process') }}"><li class="list-group-item {{ \Request::is('ingest/process') ? 'active sidebar-active' : '' }} "><i class="fas fa-hourglass-half"></i><i class="leftMenuItem"> Processing</i></li></a>
        <a href="{{ route('tasks') }}"><li class="list-group-item {{ \Request::is('ingest/tasks') ? 'active sidebar-active' : '' }} "><i class="fas fa-clock"></i><i class="leftMenuItem"> Task list</i></li></a>
        <a href="{{ route('upload') }}"><li class="list-group-item {{ \Request::is('ingest/status') ? 'active sidebar-active' : '' }} "><i class="fas fa-clipboard-check"></i><i class="leftMenuItem"> Status</i></li></a>
    @elseif (\Request::is('access/*'))
        <a href="{{ route('retrieve') }}"><li class="list-group-item {{ \Request::is('access/browse') ? 'active sidebar-active' : '' }} "><i class="fas fa-tachometer-alt"></i><i class="leftMenuItem"> Browse</i></li></a>
        <a href="{{ route('retrieve') }}"><li class="list-group-item {{ \Request::is('access/retrieve') ? 'active sidebar-active' : '' }} "><i class="fas fa-tachometer-alt"></i><i class="leftMenuItem"> Retrive</i></li></a>
    @else
        <a href="{{ route('dashboard') }}"><li class="list-group-item  {{ \Request::is('/') ? 'active sidebar-active' : '' }} "><i class="fas fa-tachometer-alt"></i><i class="leftMenuItem"> Dashboard</i></li></a>
        <a href="{{ route('dashboard') }}"><li class="list-group-item {{ \Request::is('reports') ? 'active sidebar-active' : '' }} "><i class="fas fa-chart-bar"></i><i class="leftMenuItem"> Reports</i></li></a>
    @endif
    </ul>

    <div id="poweredBy">Powered by </div>
    <div id="poweredByImg"><img src="{{asset('/images/piql_logo_white.png')}}"></div>
</div>
