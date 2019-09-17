@extends('layouts.app')

@section('top')
    @parent
@endsection

@section('content')
    <div class="contentContainer">
        <h1>{{__('access.retrieve')}}</h1>

    <ul>
    @foreach($files as $file)
        <li> {{$file->filename}} </li>
    @endforeach
    </ul> 
    <button class="btn btn-lg">Request retrieval</button>
</div>
@endsection


