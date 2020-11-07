<template>
    <div>
        <div :class="{'blurred' : isBlurred}" class="container">
            <div class="row">
                <to-dos
                    :to-dos="incomplete"
                    :user-id="userId"
                ></to-dos>
                <to-dos
                    :to-dos="complete"
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
            :to-do="activeTodoView"
        ></modal-view-todo>
        <modal-delete-todo
            :user-id="userId"
            :is-active="isDeleteTodoModalActive"
            :to-do="activeTodoDelete"
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
    props: {
        userId: {
            type: Number,
        }
    },
    created() {
        EventBus.$on('close-modal', () => {
            this.activeTodo = null;
            this.activeTodoDelete = null;
            this.activeTodoView = null;
            this.isAddTodoModalActive = false;
            this.isBlurred = false;
            this.isDeleteTodoModalActive = false;
            this.isViewTodoModalActive = false;
        });
        EventBus.$on('modal-open-manage-todo', (toDo) => {
            this.clearFileUploads();
            this.activeTodo = toDo;
            this.isBlurred = true;
            this.isAddTodoModalActive = true;
       });
        EventBus.$on('modal-open-delete-todo', (toDo) => {
            this.activeTodoDelete = toDo;
            this.isBlurred = true;
            this.isDeleteTodoModalActive = true;
        });
        EventBus.$on('modal-open-view-todo', (toDo) => {
            this.activeTodoView = toDo;
            this.isBlurred = true;
            this.isViewTodoModalActive = true;
        });
        EventBus.$on('update-todos', (toDos) => {
            this.complete = toDos.complete;
            this.incomplete = toDos.incomplete;
        });
        EventBus.$on('show-flash-message', (message, level) => {
            this.alert.level = level;
            this.alert.message = message;
            this.showAlert = true;
        });
        EventBus.$on('hide-flash-message', () => {
            this.showAlert = false;
            setTimeout(function() {
                this.alert.level = null;
                this.alert.message = null;
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
            isBlurred: false,
            isDeleteTodoModalActive: false,
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
                })
                .catch(() => EventBus.$emit('show-flash-message', '⚠️ Something went wrong', 'danger'));
        }
    }
}
</script>
