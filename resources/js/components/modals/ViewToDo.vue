<template>
    <div
        :class="{ 'is-active' : isActive }"
        aria-hidden="true"
        class="modal"
        id="view-modal"
        role="dialog"
        tabindex="-1"
    >
        <div v-if="toDo" class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ toDo.title }}</h5>
                    <button @click="closeModal" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-body-content">
                        <div class="row">
                            <div v-if="toDo.image" class="col-12 text-center">
                                <img class="todo-image round-image modal-header-image" :src="toDo.image" alt="">
                                <hr>
                            </div>
                            <div v-else class="col-12 text-center">
                                <div class="image-placeholder round-image modal-header-image">
                                    {{ toDo.title.substring(0, 1) }}
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div v-show="toDo.body" class="row">
                            <div class="col-12">
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="details">Details:</label>
                                    </div>
                                    <div class="col-9">
                                        <p id="details">
                                            {{ toDo.body }}
                                        </p>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div v-if="toDo.attachment" class="row">
                            <div class="col-12">
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="attachment">Attachment:</label>
                                    </div>
                                    <div class="col-9">
                                        <p id="attachment">
                                            <a :href="toDo.attachment.file_path" target="_blank" title="Download">
                                                {{ toDo.attachment.display_name }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div v-show="!toDo.is_complete" class="col-12">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="row margin-btm-sm">
                                            <div class="col-5 label">
                                                <label for="due_date"><i class="zmdi zmdi-calendar"></i> Due:</label>
                                            </div>
                                            <div class="col-7">
                                                <p v-if="toDo.due_date" id="due_date">
                                                    {{ getDateInDayMonthFormat(toDo.due_date) }}
                                                </p>
                                                <p v-else>
                                                    Not set.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="row margin-btm-sm">
                                            <div class="col-5 label">
                                                <label for="remind_at"><i class="zmdi zmdi-time"></i> Reminder:</label>
                                            </div>
                                            <div class="col-6">
                                                <p v-if="toDo.remind_at" id="remind_at">
                                                    {{ getDateInDayMonthFormat(toDo.remind_at) }} ({{ toDo.remind_at_time }}:00)
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
import { getDateInDayMonthFormat } from '../../helpers/dates';

export default {
    props: {
        isActive: {
            type: Boolean,
            default: false
        },
        userId: {
            type: Number,
        },
        toDo: {
            type: Object,
        },
    },
    methods: {
        getDateInDayMonthFormat,
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