<template>
    <div class="content-fluid bg-white fill-height p-3">
        <div class="row">
            <div class="col-sm subTitle pl-5">{{$t('access.browse.identityArea')}}</div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 col-sm-push-3 identifier text-right">{{$t('access.browse.identityArea.identifier')}}</div> 
            <div class="col-sm-4 col-sm-push-3">Forsvarsmuseet</div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 identifier text-right">{{$t('access.browse.identityArea.authorizedForm')}}</div>
            <div class="col-sm-4">Forsvarsmuseet</div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 identifier text-right">{{$t('access.browse.identityArea.parallelForms')}}</div>
            <div class="col-sm">
                <ul class="identityList">
                    <li>Forsvarsmuseet</li>
                    <li>Luftforsvarsmuseet</li>
                    <li>Marinemuseet</li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 identifier text-right">{{$t('access.browse.identityArea.otherForms')}}</div>
            <div class="col-sm">Forsvarets museer</div>
        </div>
        <div class="row mb-5">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 identifier text-right">{{$t('access.browse.identityArea.type')}}</div>
            <div class="col-sm">Regional</div>
        </div>

        <div class="row">
            <div class="col-sm-4 subTitle pl-5">{{$t('access.browse.contactArea')}}</div>
        </div>

        <div class="row">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 identifier text-right">{{$t('access.browse.contactArea.address')}}</div>
            <div class="col-sm">
                <ul class="addressList">
                    <li>Forsvarsmuseet</li>
                    <li>Myntgata 7-9</li>
                    <li>0151 Oslo</li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            items : {},
        }
    },

    props: {
        bagId: {
            type: String,
            default: ""
        },
    },

    async mounted() {
        /*let paginatedData = {}; //(await axios.get("/api/v1/customer/")).data;
        this.items = paginatedData.data;
         */
    },
}
</script>
