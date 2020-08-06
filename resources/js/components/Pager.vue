<template>
    <div :class="pagerBottom" v-show="hasPages">
        <nav aria-label="pages" class="d-inline-flex">
            <ul class="pagination justify-content-center">
                <li class="page-item" v-bind:class="{ disabled: onFirstPage }">
                    <a @click="firstPage" class="page-link">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                </li>
                <li class="page-item" v-bind:class="{ disabled: onFirstPage }">
                    <a @click="prevPage" class="page-link">
                        <i class="fas fa-angle-left"></i>
                    </a>
                </li>
                <li v-for="page in pages" :key="page.pageNumber" class="page-item" v-bind:class="{ active: isActivePage(page.pageNumber) }">
                    <a @click="goToPage (page.pageNumber) " class="page-link">{{page.pageNumber}}</a>
                </li>
                <li class="page-item" v-bind:class="{ disabled: onLastPage }">
                    <a @click="nextPage" class="page-link">
                        <i class="fas fa-angle-right"></i>
                    </a>
                </li>
                <li class="page-item" v-bind:class="{ disabled: onLastPage }">
                    <a @click="lastPage" class="page-link">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>

import RouterTools from '../mixins/RouterTools.js';
import axios from 'axios';

export default {
    name: 'Pager',
    mixins: [ RouterTools ],
    mounted() {
        /*
        Paging is driven by the metadata block returned from the REST API,
        and unlike the other filters needs to special mount handling
         */
    },
    props: {
        meta: {
            type: Object,
            default: {}
        },
        visiblePageSelectors: {
            type: Number,
            default: 20
        },
        height: {
            type: Number,
            default: 0
        }
    },
    computed: {
        hasPages: function() {
            return this.numberOfPages > 1;
        },
        currentHeight: function() {
            return this.height;
        },
        next: function() {
            return this.meta && this.meta.current_page < this.meta.last_page ? this.meta.current_page + 1 : null;
        },
        prev: function() {
            return this.meta && this.meta.current_page > 1 ? this.meta.current_page - 1 : null;
        },
        onFirstPage: function() {
            return this.meta && this.prev === null;
        },
        onLastPage: function() {
            return this.meta && this.next === null;
        },
        numberOfPages: function() {
            return this.meta ? this.meta.last_page : null;
        },
        splitVisible: function() {
            let m = this.visiblePageSelectors > this.numberOfPages ? this.numberOfPages : this.visiblePageSelectors;
            return Math.floor( m / 2 );
        },
        firstVisible: function() {
            let vis = this.visiblePageSelectors > this.numberOfPages ? this.numberOfPages : this.visiblePageSelectors;
            let no = this.numberOfPages;
            if( vis >= no ) {
                return 1;
            }

            let d = this.currentPage - this.splitVisible;
            return d < 1 ? 1 : d;
        },
        currentPage: function() {
            return this.meta ? this.meta.current_page : null;
        },
        pages: function() {
            let first = this.firstVisible;
            let split = this.splitVisible;
            let current = this.currentPage;
            let len = this.visiblePageSelectors > this.numberOfPages ? this.numberOfPages : this.visiblePageSelectors;
            let last = this.numberOfPages;

            if(current + split > last) {
                first = last-len+1;
                if(first < 1)
                    first = 1;
            }

            let self = this;
            return Array.from( { length: len }, ( _ , n ) => {
                let pageNumber = first + n;
                return { pageNumber: pageNumber, isActive: pageNumber === self.currentPage };
            } );
        },
        pagerBottom: function() {
            return this.height > 1000 ? "fixed-bottom pagerBottom" : "mt-1 pt-1";
        }
    },
    methods: {
        isActivePage( pageNumber ) {
            let qp = this.$route.query.page ?? 1;
            return qp == pageNumber;
        },
        nextPage() {
            let page = this.next > 1 ? this.next : null;
            this.updateQueryParams({ page });
        },
        prevPage() {
            let page = this.prev > 1 ? this.prev : null;
            this.updateQueryParams({ page });
        },
        firstPage() {
            this.updateQueryParams({ page: null });
        },
        lastPage() {
            this.updateQueryParams({ page: this.numberOfPages });
        },
        goToPage( page ) {
            if( isNaN(page) || page < 2 ) page = null;
            this.updateQueryParams({ page });
        }
    }
}
</script>
