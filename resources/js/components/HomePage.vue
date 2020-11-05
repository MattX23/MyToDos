<template>
    <div>
        <div :class="{'blurred' : isBlurred}" class="container">
            <div class="row">
                <to-dos
                    :todos="incomplete"
                    :user-id="userId"
                ></to-dos>
                <to-dos
                    :todos="complete"
                    :complete="true"
                    :user-id="userId"
                ></to-dos>
            </div>
        </div>
        <modal-manage-todo
            :active-to-do="activeTodo"
            :is-active="isAddTodoModalActive"
            :user-id="userId"
        ></modal-manage-todo>
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
        <alert
            :level="alert.level"
            :message="alert.message"
            :show-alert="showAlert"
        ></alert>
    </div>
</template>

<script>
import { EventBus } from "../eventbus/event-bus";
import { TWO_SECOND_TIMEOUT } from "../helpers/constants";

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
        EventBus.$on('modal-open-manage-todo', (todo) => {
            this.clearFileUploads();
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
        EventBus.$on('show-flash-message', (message, level) => {
            this.showAlert = true;
            this.alert.message = message;
            this.alert.level = level;
        });
        EventBus.$on('hide-flash-message', () => {
            this.showAlert = false;
            setTimeout(function() {
                this.alert.message = null;
                this.alert.level = null;
            }, TWO_SECOND_TIMEOUT)
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
            alert: {
                level: null,
                message: '',
            },
            complete: [],
            incomplete: [],
            isAddTodoModalActive: false,
            isDeleteTodoModalActive: false,
            isBlurred: false,
            isViewTodoModalActive: false,
            showAlert: false,
        }
    },
    methods: {
        clearFileUploads() {
            document.getElementById('file').value = "";
            document.getElementById('image').value = "";
        },
        getToDos() {
            axios.get(GET_TO_DOS_ROUTE+this.$props.userId)
                .then(response => {
                    this.complete = response.data.complete;
                    this.incomplete = response.data.incomplete;
                    EventBus.$emit('show-flash-message', '⚠️ Something went wrong', 'danger')
                })
                .catch(() => EventBus.$emit('show-flash-message', '⚠️ Something went wrong', 'danger'));
        }
    }
}
</script>
