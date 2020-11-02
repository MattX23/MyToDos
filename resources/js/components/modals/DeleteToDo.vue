<template>
    <div
            :class="{ 'is-active' : isActive }"
            aria-labelledby=""
            aria-hidden="true"
            class="modal"
            id="input-modal"
            role="dialog"
            tabindex="-1"
    >
        <div v-if="todo" class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete To Do</h5>
                    <button
                        @click="closeModal"
                        aria-label="Close"
                        class="close"
                        data-dismiss="modal"
                        type="button"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-body-content">
                        <div class="row">
                            <div class="col-12">
                                Are you sure you want to delete this To Do?

                                <div class="modal-body-text">"{{ todo.title }}"</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        @click="closeModal"
                        class="btn btn-danger"
                        type="button"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteToDo"
                        class="btn btn-success"
                        type="button"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { EventBus } from "../../eventbus/event-bus";

const DELETE_TO_DO_ROUTE = '/api/delete-to-do/';

export default {
    props: {
        isActive: {
            type: Boolean,
            default: false
        },
        todo: {
            type: Object,
            default: null,
        },
        userId: {
            type: Number,
        }
    },
    methods: {
        closeModal() {
            EventBus.$emit('close-modal');
        },
        deleteToDo() {
            axios.delete(`${DELETE_TO_DO_ROUTE}${this.$props.todo.id}/${this.$props.userId}`)
                .then(response => {
                    this.closeModal();
                    EventBus.$emit('update-todos', response.data);
                })
        },
    }
}
</script>

<style lang="scss" scoped>
.modal-body-text {
    margin-top: 20px;
    font-style: italic;
    text-align: center;
}
</style>