<template>
    <div class="container">
        <to-dos
            :todos="incomplete"
        ></to-dos>
    </div>
</template>

<script>
const GET_TO_DOS_ROUTE = '/api/get-to-dos';

export default {
    mounted() {
        this.getToDos();
    },
    data() {
        return {
            complete: [],
            incomplete: [],
        }
    },
    methods: {
        getToDos() {
            axios.get(GET_TO_DOS_ROUTE)
                .then(response => {
                    this.complete = response.data.complete;
                    this.incomplete = response.data.incomplete;
                })
                .catch(error => console.log(error))
        }
    }
}
</script>
