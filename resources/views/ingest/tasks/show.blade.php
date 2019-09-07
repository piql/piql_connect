@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
<div>
    <i class="fas fa-list-ul titleIcon"></i>{{__("ingest.fileList.filesIn")}}&nbsp;<span class="noTextTransform">{{ $bag->name }}</span>
</div>
@endsection

@section('content')
    <div class="contentContainer">
        <em>{{__("ingest.fileList.ingress")}}</em>
        {{ Breadcrumbs::render('file_list', $bag) }}
        <file-list :bag-id="'{{ $bag->id }}'">
        </file-list>
    </div>
@endsection
