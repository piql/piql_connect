@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-hourglass-half titleIcon"></i>{{ __('ingest.processing.header') }}
@endsection

@section('content')
    <div class="contentContainer">
        <div class="col-8">
            <em class="mb-3 mt-2">
            {{ __('ingest.processing.ingress') }}
            </em>
        </div>
        <Processing>
        </Processing>
    </div>
@endsection
