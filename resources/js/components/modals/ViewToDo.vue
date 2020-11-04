<template>
    <div
        :class="{ 'is-active' : isActive }"
        class="modal"
        aria-labelledby=""
        aria-hidden="true"
        id="view-modal"
        role="dialog"
        tabindex="-1"
    >
        <div v-if="todo" class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ todo.title }}</h5>
                    <button @click="closeModal" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-body-content">
                        <div class="row">
                            <div v-if="todo.image" class="col-12 text-center">
                                <img class="todo-image round-image modal-header-image" :src="todo.image" alt="">
                                <hr>
                            </div>
                            <div v-else class="col-12 text-center">
                                <div class="image-placeholder round-image modal-header-image">{{ todo.title.substring(0, 1) }}</div>
                                <hr>
                            </div>
                        </div>
                        <div v-if="todo.body" class="row">
                            <div class="col-12">
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="details">Details:</label>
                                    </div>
                                    <div class="col-9">
                                        <p id="details">
                                            {{ todo.body }}
                                        </p>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div v-if="todo.attachment" class="row">
                            <div class="col-12">
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="attachment">Attachment:</label>
                                    </div>
                                    <div class="col-9">
                                        <p id="attachment">
                                            <a :href="todo.attachment.file_path" target="_blank" title="Download">{{ todo.attachment.display_name }}</a>
                                        </p>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div v-if="!todo.is_complete" class="col-12">
                                <div class="row">
                                    <div class="col-5">
                                        <div class="row margin-btm-sm">
                                            <div class="col-5 label">
                                                <label for="due_date"><i class="zmdi zmdi-calendar"></i> Due:</label>
                                            </div>
                                            <div class="col-7">
                                                <p v-if="todo.due_date" id="due_date">
                                                    {{ todo.due_date }}
                                                </p>
                                                <p v-else>
                                                    Not set.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-7">
                                        <div class="row margin-btm-sm">
                                            <div class="col-5 label">
                                                <label for="remind_at"><i class="zmdi zmdi-time"></i> Reminder:</label>
                                            </div>
                                            <div class="col-6">
                                                <p v-if="todo.remind_at" id="remind_at">
                                                    {{ todo.remind_at }}
                                                </p>
                                                <p v-else>
                                                    Not set.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { EventBus } from "../../eventbus/event-bus";

export default {
    props: {
        isActive: {
            type: Boolean,
            default: false
        },
        userId: {
            type: Number,
        },
        todo: {
            type: Object,
        },
    },
    methods: {
        closeModal() {
            EventBus.$emit('close-modal');
        },
    }
}
</script>

<style lang="scss" scoped>
.label {
    font-weight: bold;
    padding-top: 7px;
}
.modal-header-image {
    display: inline-block;
    border-radius: 10px;
    float: none;
}
</style>