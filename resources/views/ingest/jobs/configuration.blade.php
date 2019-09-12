@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-cog titleIcon"></i>{{__('ingest.offlineStorage.contentOptions.header')}}
@endsection

@section('content')
    <em>
        {{__('ingest.offlineStorage.contentOptions.ingress')}}
    </em>
    {{ Breadcrumbs::render('content_options_view', $job) }}
    <div class="contentContainer">
        <div class="container-fluid">
            <div class="row plistHeader">
                <div class="col-sm-4">{{__('ingest.offlineStorage.jobName')}}</div>
                <div class="col-sm-2 pr-3">{{__('ingest.offlineStorage.numberOfAips')}}</div>
                <div class="col-sm-2 pr-3">{{__('ingest.offlineStorage.size')}}</div>
                <div class="col-sm-4 listActionItems">&nbsp;</div>
            </div>

            <job-list-item  :item="{{json_encode($job)}}"
                            :job-list-url="'/api/v1/ingest/offline_storage/pending'"
                            :action-icons="{list: false, metadata: false, config: false, delete: false, defaultAction: true}"/>
        </div>

        <div class="list">
            <table>
                <tbody>
                <tr class="contentOptionsRow">
                    <td colspan="5">
                        <label for="outputMatching">Output matching</label>
                        <textarea id="outputMatching"></textarea><br>
                        <label for="layout">Layout</label>
                        <textarea id="layout"></textarea><br>
                        <label for="reelDef">Reel definition</label>
                        <textarea id="reelDef"></textarea><br>
                        <br>
                        <span class="contentOptionsUploadTag">CLIENT LOGO START</span>
                        <input type="file" name="file-1[]" id="file-1" class="inputfile inputfile-1" data-multiple-caption="{count} files selected" multiple="">
                        <label class="filelabel" for="file-1">
                            <span>CHOOSE FILE</span></label>

                        <br>

                        <span class="contentOptionsUploadTag">CLIENT REEL DESCRIPTION START</span>
                        <input type="file" name="file-2[]" id="file-2" class="inputfile inputfile-2" data-multiple-caption="{count} files selected" multiple="">
                        <label class="filelabel" for="file-2">
                            <span>CHOOSE FILE</span></label>

                        <br>

                        <span class="contentOptionsUploadTag">JPG FORMAT</span>
                        <input type="file" name="file-3[]" id="file-3" class="inputfile inputfile-3" data-multiple-caption="{count} files selected" multiple="">
                        <label class="filelabel" for="file-3">
                            <span>CHOOSE FILE</span></label>  <br>

                        <div style="width: 100%; text-align: right;">
                            <input type="submit" class="inputSubmitCancel" value="Cancel">
                            <input type="submit" class="inputSubmitSave" value="Save">
                        </div>
                    </td>
                </tr>
                </tbody></table>
        </div>
    </div>
@endsection
