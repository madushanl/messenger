<template>
    <div class="row align-items-center">
        <div class="col-12">
            <h3 class="text-center">Sorry! We do not support for this browser ☹️</h3>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            twemoji.size = '16x16';
            twemoji.parse(document.body);
        }
    }
</script>
