@extends('layouts.app');

<div id="loginPage">
    <div id="loginForm">
        <a href="#"><img src="{{ asset('images/piql-connect.png', 'piqlConnect logo') }}"/></a> 
            <form method="post" id="theForm" action="/">
                <label for="username">Username</label><br>
                <input type="text" id="username" name="username" value="user"><br>
                <label for="password">Password</label><br>
                <input type="password" id="password" name="password" value="secret"><br>
                <input type="submit" value="Log in"><br>
				{{ csrf_field() }}
                <span class="forgot"><a href="forgot_password.html">Forgot your password?</a></span>
            </form>
    </div>
</div>
