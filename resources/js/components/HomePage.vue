<template>
    <div>
        <completed-to-dos></completed-to-dos>
    </div>
</template>

<script>
const GET_TO_DOS_ROUTE = '/api/get-to-dos';

export default {
    mounted() {
        this.getToDos();
    },
    methods: {
        getToDos() {
            axios.get(GET_TO_DOS_ROUTE)
                .then(response => console.log(response.data))
        }
    }
}
</script>
