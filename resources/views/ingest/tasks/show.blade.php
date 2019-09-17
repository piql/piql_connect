@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
<div>
    <i class="fas fa-list-ul titleIcon"></i>{{__("ingest.fileList.header")}}&nbsp;<span class="noTextTransform"></span>
</div>
@endsection

@section('content')
    <div class="contentContainer">
        <div class="col-6">
            <em class="mb-3 mt-2">
                {{__("ingest.fileList.ingress")}}
            </em>
        </div>
        <div class="col-8 pb-4">
            {{ Breadcrumbs::render('file_list', $bag) }}
        </div>
        <file-list :bag-id="'{{ $bag->id }}'">
        </file-list>
    </div>
@endsection
