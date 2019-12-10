<template>
    <nav aria-label="pages" class="mt-2 mb-1 d-inline-flex ">
        <ul class="pagination pagination-sm justify-content-center">
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
            <li v-for="page in pages" class="page-item" v-bind:class="{ active: page.active}">
                <a @click="goToPage (page) " class="page-link">{{page.page}}</a>
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
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
            }
        },
        props: {
            meta: null,
            visiblePageSelectors: {
                type: Number,
                default: 20
            }
        },

        computed: {
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
            path: function() {
                return this.meta ? this.meta.path : null;
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
                    let page = first + n;
                    return self.buildPageUrlWrapper( page );
                } );
            },
        },
        methods: {
            buildPageUrlWrapper(page) {
                let query = 'page='+page;
                let path = this.path;
                let currentPage = this.currentPage;
                return ({ 'page': page, 'active' : page === currentPage,  'url': path + '?' + query, 'query': query });
            },
            nextPage() {
                this.$emit('updatePage', this.buildPageUrlWrapper(this.next));
            },
            prevPage() {
                this.$emit('updatePage', this.buildPageUrlWrapper(this.prev));
            },
            firstPage() {
                this.$emit('updatePage', this.buildPageUrlWrapper(1));
            },
            lastPage() {
                this.$emit('updatePage', this.buildPageUrlWrapper(this.numberOfPages) );
            },
            goToPage( pageWrapper ) {
                this.$emit('updatePage', pageWrapper );
            }
        }
    }
</script>
