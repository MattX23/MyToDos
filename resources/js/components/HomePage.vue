<template>
    <div>
        <completed-to-dos></completed-to-dos>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.getToDos();
        },
        methods: {
            getToDos() {
                axios.get()
                    .then()
            }
        }
    }
</script>
