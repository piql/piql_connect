@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-hourglass-half titleIcon"></i>{{ __('ingest.processing.header') }}
@endsection

@section('content')
    <div class="contentContainer">
        <em>
            {{ __('ingest.processing.ingress') }}
        </em><br/>
        <Processing>
        </Processing>
    </div>
@endsection
