<template>
    <div class="w-100">
        <page-heading icon="fa-tags" :title="$t('access.metadata.file.title')" :ingress="$t('access.metadata.file.ingress')" />
        <dublin-core baseUrl="/api/v1/access/files"i :readOnly="true"> </dublin-core>
    </div>
</template>

<script>
export default {
}
</script>
