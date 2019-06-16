@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-hourglass-half titleIcon"></i>Processing
@endsection

@section('content')
    <div class="contentContainer">
        <Processing>
        </Processing>
    </div>
@endsection
