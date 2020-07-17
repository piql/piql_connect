<template>
	<div class="subTitle">
    	<span v-if="subTitle">
			<i class="fas fa-chevron-right"></i>
			<router-link :to="subTitleRoute" class="subTitleLink" v-if="subTitleRoute != null">{{subTitle}}</router-link>
			<span v-if="subTitleRoute == null" @click="$router.go(-1)" class="cursorPointer">{{subTitle}}</span>
		</span>
    </div>
</template>

<script>
	export default {
		props: {
			subTitle: {
				type: String,
			},
			subTitleRoute: {
				type: Object,
				default: null
			},
		}
	}
</script>

<style scoped>
	.subTitle {
		margin-top: 10px;
		margin-left: 84px;
		font-weight: bold;
	}
	.subTitle a {
		color: #cc5d33;
	}
</style>