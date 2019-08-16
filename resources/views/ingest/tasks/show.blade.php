@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
<div>
    <i class="fas fa-list-ul titleIcon"></i>Files in <span class="noTextTransform">{{ App\Bag::find($bagId)->name }}</span>
</div>
@endsection

@section('content')
    <!-- for future use -->
    <em>Short descriptive text of view.</em>
    <div>
        <a href="{{ URL::previous() }}">
            <i class="breadcrumbs noTextTransform">&lt;&lt; Back</i>
        </a>
    </div>
    <file-list :bag-id="'{{ $bagId }}'">
    </file-list>
@endsection
