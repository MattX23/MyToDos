<template>
    <div>
        <div :class="{'blurred' : isBlurred}" class="container">
            <to-dos
                :todos="incomplete"
            ></to-dos>
        </div>
        <modal-add-todo
            :active-to-do="activeTodo"
            :is-active="isAddTodoModalActive"
            :user-id="userId"
        ></modal-add-todo>
        <modal-view-todo
            :user-id="userId"
            :is-active="isViewTodoModalActive"
            :todo="activeTodoView"
        ></modal-view-todo>
        <modal-delete-todo
            :user-id="userId"
            :is-active="isDeleteTodoModalActive"
            :todo="activeTodoDelete"
        ></modal-delete-todo>
    </div>
</template>

<script>
import { EventBus } from "../eventbus/event-bus";

const GET_TO_DOS_ROUTE = '/api/get-to-dos/';

export default {
    props: ['userId'],
    created() {
        EventBus.$on('close-modal', () => {
            this.isAddTodoModalActive = false;
            this.isDeleteTodoModalActive = false;
            this.isViewTodoModalActive = false;
            this.activeTodo = null;
            this.activeTodoDelete = null;
            this.activeTodoView = null;
            this.isBlurred = false;
        });
        EventBus.$on('modal-open-add-todo', (todo) => {
            this.isAddTodoModalActive = true;
            this.activeTodo = todo;
            this.isBlurred = true;
       });
        EventBus.$on('modal-open-delete-todo', (todo) => {
            this.isDeleteTodoModalActive = true;
            this.activeTodoDelete = todo;
            this.isBlurred = true;
        });
        EventBus.$on('modal-open-view-todo', (todo) => {
            this.isViewTodoModalActive = true;
            this.activeTodoView = todo;
            this.isBlurred = true;
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
            activeTodo: null,
            activeTodoDelete: null,
            activeTodoView: null,
            complete: [],
            incomplete: [],
            isAddTodoModalActive: false,
            isDeleteTodoModalActive: false,
            isBlurred: false,
            isViewTodoModalActive: false,
        }
    },
    methods: {
        getToDos() {
            axios.get(GET_TO_DOS_ROUTE+this.$props.userId)
                .then(response => {
                    this.complete = response.data.complete;
                    this.incomplete = response.data.incomplete;
                });
        }
    }
}
</script>
