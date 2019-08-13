<template>
    <div class="bg-white fill-height p-3">
        <div class="row">
            <div class="col-sm subTitle pl-5">Identity area</div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 col-sm-push-3 identifier text-right">Identifier</div> 
            <div class="col-sm-4 col-sm-push-3">iKA Kongsberg</div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 identifier text-right">Authorized form of name</div>
            <div class="col-sm-4">iKA Kongsberg</div>
        </div>
        <div class="row mb-1">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 identifier text-right">Parallel form(s) of name</div>
            <div class="col-sm">
                <ul class="identityList">
                    <li>iKA Kongsberg</li>
                    <li>Inter Kommunalt Arkiv Kongsberg</li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 identifier text-right">Other form(s) of name</div>
            <div class="col-sm">iKA Kongsberg</div>
        </div>
        <div class="row mb-5">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 identifier text-right">Type</div>
            <div class="col-sm">Regional</div>
        </div>

        <div class="row">
            <div class="col-sm-4 subTitle pl-5">Contact area</div>
        </div>

        <div class="row">
            <div class="col-sm-1">&nbsp;</div>
            <div class="col-sm-4 identifier text-right">Address</div>
            <div class="col-sm">
                <ul class="addressList">
                    <li>iKA Kongsberg</li>
                    <li>Gateadresse 1</li>
                    <li>303 Kongsberg</li>
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
        //        let paginatedData = (await axios.get("/api/v1/customer/")).data;
        this.items = paginatedData.data;
    },
}
</script>
