<template>
    <div>
        <div class="row plist">
            <div class="col"><input type="checkbox" id="{item.uuid}" class="checkbox"></div>
            <div class="col">
                <a href="">
                    <span class="linked">
                {{item.uuid}}
                    </span>
                </a>
            </div>
            <div class="col">
                {{item.fileName}}
            </div>
            <div class="col">
                {{item.created_at}}
            </div>
            <div class="col listActionItems">
                <i class="fas fa-cog"></i>&nbsp;
                <i class="fas fa-trash-alt"></i>&nbsp;
            </div>
            <div class="col piqlIt">&nbsp;</div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        console.log('FileInProcess component mounted.')
    },
    props: {
        item: Object
    }
}
</script>
