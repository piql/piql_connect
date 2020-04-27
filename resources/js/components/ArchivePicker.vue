<template>
    <div class="form-group w-100">
        <label v-if="showLabel" for="archivePicker" class="col-form-label">
            {{label}}
        </label>
        <select v-model="selection" id="archivePicker" class="form-control w-100" v-bind:disabled="selectionDisabled" data-live-search="true">
            <option v-for="archive in archivesWithWildcard" v-bind:value="archive.uuid">
                {{archive.title}}
            </option>
        </select>
    </div>
</template>

<script>
import RouterTools from '../mixins/RouterTools.js';

export default {
    mixins: [ RouterTools ],

    mounted() {
        axios.get("/api/v1/planning/archives").then( (response) => {
            this.archives = response.data.data;
        });
    },
    methods: {
        dispatchRouting: function() {
            let query = this.$route.query;
            Vue.nextTick( () => {
                this.updatePicker( query.archive );
            });
        },
        refreshPicker: function() {
            $(`#${this.elementId}`).selectpicker('refresh');
        },
        updatePicker: function( archive ) {
            $(`#${this.elementId}`).selectpicker('val', archive);
            this.refreshPicker();
        }
    },
    data() {
        return {
            selection: null,
            archives: [],
            initComplete: false,
        };
    },
    props: {
        selectionDisabled: false,
        useWildCard: true,
        wildCardLabel: {
            type: String,
            default: "All"
        },
        label: {
            type: String,
            default: null
        },
        elementId: {
            type: String,
            default: "archivePicker"
        }
    },
    watch: {
        '$route': 'dispatchRouting',

        selection: function ( archive ){
            if( !this.initComplete ) {
                /* don't change query params when setting selection from page load */
                this.initComplete = true;
                return;
            }

            if( archive === '0' ){
                this.updateQueryParams({ archive : null, page: null, holding: null });
            } else {
                this.updateQueryParams({ archive, page : null, holding: null });
            }
        },
        archives: function( archives ){
            if( !! archives ) {
                let archiveQuery = this.$route.query.archive ?? '0';
                Vue.nextTick( () => {
                    this.updatePicker( archiveQuery );
                    Vue.nextTick( () => {
                        this.refreshPicker();
                    });
                });
            }
        },

    },
    computed: {
        showLabel: function() {
            return this.label.length > 0;
        },
        archivesWithWildcard: function() {
            /* If it has elements, push a wildcard element ("All") at the start of the list */
            return this.archives && this.archives.length > 1
                ? [ {'id' : 0, 'title': this.wildCardLabel, 'uuid': '0' }, ...this.archives ]
                : this.archives;
        }
    }
}

</script>
