<template>
    <div>
        <div class="mb-2 mt-2">
            <div class="row">
                <div class="col-sm-1 text-left">
                    <i class="fas fa-clipboard-check titleIcon"></i>
                </div>
                <div class="col-sm-6 text-left">
                    <h1>{{$t("ingest.offlineStorage.status.header")}}</h1>
                </div>
            </div> 
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-6 text-left">
                    <div class="ingressText">{{$t("ingest.offlineStorage.status.ingress")}}</div>
                </div>
            </div>
        </div>

        <status-list :job-list-url="'/api/v1/ingest/offline_storage/archive'"/>
    </div>
</template>

<script>
export default  {
    data() {
    },
}
