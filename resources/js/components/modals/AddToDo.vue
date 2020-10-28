<template>
    <div
            :class="{ 'is-active' : isActive }"
            class="modal"
            aria-labelledby=""
            aria-hidden="true"
            id="input-modal"
            role="dialog"
            tabindex="-1"
    >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create a To Do</h5>
                    <button @click="closeModal" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-body-content">
                        <div class="row">
                            <div class="col-12">
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="title">Title:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="title" placeholder="Give your To Do a name">
                                    </div>
                                </div>
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="details">Details:</label>
                                    </div>
                                    <div class="col-9">
                                        <textarea class="form-control" rows="3" id="details" placeholder="Additional info"></textarea>
                                    </div>
                                </div>
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="due_date">Due Date:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="date" class="form-control" id="due_date">
                                    </div>
                                </div>
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="remind_at">Remind me:</label>
                                    </div>
                                    <div class="col-9">
                                        <select id="remind_at" class="form-control">
                                            <option disabled selected>Never</option>
                                            <option>1 day before due date</option>
                                            <option>2 days before due date</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="image">Image:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" id="image" accept="image/*">
                                    </div>
                                </div>
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="file">Attachment:</label>
                                    </div>
                                    <div class="col-9">
                                        <input type="file" id="file">
                                    </div>
                                </div>
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
                        class="btn btn-success"
                        type="button"
                    >
                        Create
                    </button>
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
</style>