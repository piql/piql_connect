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
            <li v-for="page in pages" class="page-item" v-bind:class="{ active: page.isActive}">
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
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
            }
        },
        mounted() {
            let page = parseInt( this.$route.query.page );
            if( page ) {
                if( page < 1 ) {
                    const query = Object.assign( {}, this.$route.query );
                    query.page = 1;
                    this.$router.replace( { query } );
                }
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
        },
        methods: {
            updateQueryParam( page ) {
                const query = Object.assign( {}, this.$route.query );
                if( page != query.page ) {
                    query.page = page;
                    this.$router.push({ query });
                }
            },
            nextPage() {
                this.updateQueryParam( this.next );
            },
            prevPage() {
                this.updateQueryParam( this.prev );
            },
            firstPage() {
                this.updateQueryParam( 1 );
            },
            lastPage() {
                this.updateQueryParam( this.numberOfPages );
            },
            goToPage( page ) {
                this.updateQueryParam( page );
            }
        }
    }
</script>
