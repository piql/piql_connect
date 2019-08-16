@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
<div>
    <i class="fas fa-tags titleIcon"></i><span class="noTextTrasform">Metadata</span>
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

    <div id="content">

        <!--<i class="breadcrumbs">Task list &gt; 827546892-2957-590-4927-G8285729 &gt; Content options &gt; Metadata</i>-->

        <div class="list">
            <table>
                <thead>
                <tr><th>Job ID</th>
                    <th>Content</th>
                    <th>Date</th>
                </tr></thead>
                <tbody><tr>
                    <td>{{ $file->uuid }}</td>
                    <td>{{ $file->filename }}</td>
                    <td>{{  $file->created_at }}</td>
                </tr>
                <tr class="contentOptionsRow">
                    <td colspan="5">
                        <label for="metadatafield1">Metadata field 1</label>
                        <input type="text" id="metadatafield1"><br>
                        <label for="metaatafield2">Metadata field 2</label>
                        <input type="text" id="metaatafield2"><br>
                        <label for="metadatafield3">Metadata field 3</label>
                        <textarea id="metadatafield3"></textarea><br>
                        <br>
                        <div style="width: 100%; text-align: right;">
                            <input type="submit" class="inputSubmitCancel" value="Cancel">
                            <input type="submit" class="inputSubmitSave" value="Save">
                        </div>
                    </td>
                </tr>
                </tbody></table>
        </div>
    </div>


    <!--<file-list :bag-id="'{{ $bag->id }}'">
    </file-list>-->
@endsection
