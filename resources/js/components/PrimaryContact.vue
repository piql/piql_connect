<template>
    <div> 
        <div class="row">
            <div class="col identfier">Primary Contact</div>
        </div>
        <div class="row">
            <div class="col m-0">
                <ul class="contactItems m-0">
                    <li>iKA Kongsberg</li>
                    <li>John Doe</li>
                    <li>+47 50 40 30 20</li>
                    <li><a href="mailto:john.doe@ika.no">john@doe.ika.no</a></li>
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
