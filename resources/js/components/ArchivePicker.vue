<template>
    <div class="form-group w-100">
        <span v-if="!singleArchive">
            <label v-if="showLabel" for="archivePicker" class="col-form-label-sm">
            {{label}}
        </label>
        <div :class="inputValidation">
            <select v-model="selection" :id="elementId" class="form-control w-100" v-bind:disabled="selectionDisabled" :data-none-selected-text="wildCardLabel" data-live-search="true" @change="selChange">
                <option v-for="archive in archivesWithWildcard" :key="archive.id" :value="archive.uuid">
                    {{archive.title}}
                </option>
            </select>
        </div>
    </span>
    <span v-else>
        <label class="col-form-label-sm" for="singleArchive">{{$t('Archive')}}</label>
        <select class="form-control text-center" disabled>
            <option selected>
                {{singleArchiveTitle}}
            </option>
        </select>
    </span>
    </div>
</template>

<script>
import RouterTools from '../mixins/RouterTools.js';

export default {
    mixins: [ RouterTools ],

    mounted() {
        axios.get("/api/v1/metadata/archives").then( (response) => {
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
            this.selChange();
        },
        selChange: function () {
            let val = $(`#${this.elementId}`).val();
            this.inputValidation = this.required && (!val || val == '0') ? 'mustFill' : '';
        }
    },
    data() {
        return {
            selection: null,
            archives: [],
            initComplete: false,
            inputValidation: '',
        };
    },
    props: {
        singleArchive: {
            type: Boolean,
            default: false
        },
        singleArchiveTitle: {
            type: String,
            default: "Your Archive"
        },
        selectionDisabled: {
            type: Boolean,
            default: false
        },
        useWildCard: {
            type: Boolean,
            default: true
        },
        wildCardLabel: {
            type: String
        },
        label: {
            type: String,
            default: null
        },
        elementId: {
            type: String,
            default: "archivePicker"
        },
        required: {
            type: Boolean,
            default: false
        },
    },
    watch: {
        '$route': 'dispatchRouting',

        selection: function ( archive ){
            if( archive === '0' ){
                this.updateQueryParams({ archive : null, page: null, holding: null });
            } else {
                this.updateQueryParams({ archive, page : null, holding: null });
            }

            this.$emit('loadNewHolders')
        },
        archives: function( archives ){
            if( !! archives ) {
                let archiveQuery = this.$route.query.archive ?? '0';
                    this.refreshPicker();
                    Vue.nextTick( () => {
                        this.updatePicker( archiveQuery );
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
