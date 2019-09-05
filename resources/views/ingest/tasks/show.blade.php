@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
<div>
    <i class="fas fa-list-ul titleIcon"></i>{{__("ingest.fileList.filesIn")}}&nbsp;<span class="noTextTransform">{{ App\Bag::find($bagId)->name }}</span>
</div>
@endsection

@section('content')
    <div class="contentContainer">
        <em>{{__("ingest.fileList.ingress")}}</em>
        <div>
            <a href="{{ URL::previous() }}">
                <i class="breadcrumbs noTextTransform">&lt;&lt; Back</i>
            </a>
        </div>
        <file-list :bag-id="'{{ $bagId }}'">
        </file-list>
    </div>
@endsection
