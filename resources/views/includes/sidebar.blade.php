<div class="wrapper sideMenu">
    <div id="welcomeText">
        Welcome<br><i class="fas fa-user"></i> {{ Auth::user()->full_name }}
    </div>
    <div id="collapseSideMenu">
        <img onclick="collapseMenu()" src="{{asset('images/collapsemenu.png')}}">
    </div>
    <div class="clearboth"></div>
    <ul class="list-group">
        <a href="/"><li class="list-group-item sidebar-active active"><i class="fas fa-tachometer-alt"></i><i class="leftMenuItem"> Dashboard</i></li></a>
        <a href="reports"><li class="list-group-item"><i class="fas fa-chart-bar"></i><i class="leftMenuItem"> Reports</i></li></a>
    </ul>

    <div id="poweredBy">Powered by </div>
    <div id="poweredByImg"><img src="{{asset('images/piql_logo_white.png')}}"></div>
</div>
