<template>
    <div> 
        <div class="row">
            <div class="col identfier">{{$t('access.browse.primaryContact')}}</div>
        </div>
        <div class="row">
            <div class="col m-0">
                <ul class="contactItems m-0">
                    <li>Forsvarsmuseet</li>
                    <li><i class="fas fa-phone-alt"></i> +47 23 09 35 82</li>
                    <li>&nbsp;&nbsp;<a href="mailto:post.fmu@mil.no">post.fmu@mil.no</a></li>
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
        /*let paginatedData = (await axios.get("/api/v1/customer/")).data;
        this.items = paginatedData.data;
         */
    },
}
</script>
