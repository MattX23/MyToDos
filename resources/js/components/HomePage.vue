<template>
    <div>
        <div :class="{'blurred' : isBlurred}" class="container">
            <to-dos
                :todos="incomplete"
            ></to-dos>
        </div>
        <modal-add-todo
            :user-id="userId"
            :is-active="isAddTodoModalActive"
        ></modal-add-todo>
        <modal-view-todo
            :user-id="userId"
            :is-active="isViewTodoModalActive"
            :todo="activeTodo"
        ></modal-view-todo>
    </div>
</template>

<script>
import { EventBus } from "../eventbus/event-bus";

const GET_TO_DOS_ROUTE = '/api/get-to-dos/';

export default {
    props: ['userId'],
    created() {
       EventBus.$on('modal-open-add-todo', () => {
            this.isAddTodoModalActive = true;
            this.isBlurred = true;
       });
        EventBus.$on('modal-open-view-todo', (todo) => {
            this.isViewTodoModalActive = true;
            this.activeTodo = todo;
            this.isBlurred = true;
        });
        EventBus.$on('close-modal', () => {
            this.isAddTodoModalActive = false;
            this.isViewTodoModalActive = false;
            this.isBlurred = false;
        });
        EventBus.$on('update-todos', (todos) => {
            this.complete = todos.complete;
            this.incomplete = todos.incomplete;
        });
    },
    mounted() {
        this.getToDos();
    },
    data() {
        return {
            complete: [],
            incomplete: [],
            isBlurred: false,
            isAddTodoModalActive: false,
            isViewTodoModalActive: false,
            activeTodo: null,
        }
    },
    methods: {
        getToDos() {
            axios.get(GET_TO_DOS_ROUTE+this.$props.userId)
                .then(response => {
                    this.complete = response.data.complete;
                    this.incomplete = response.data.incomplete;
                })
        }
    }
}
</script>
