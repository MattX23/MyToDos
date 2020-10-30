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
                                        <input
                                            v-model="todo.title"
                                            @keydown="clearError('title')"
                                            type="text"
                                            class="form-control"
                                            id="title"
                                            placeholder="Give your To Do a name"
                                        >
                                        <span v-if="errors.title" class="error">{{ errors.title }}</span>
                                    </div>
                                </div>
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="details">Details:</label>
                                    </div>
                                    <div class="col-9">
                                        <textarea
                                            @keydown="clearError('body')"
                                            v-model="todo.body"
                                            class="form-control"
                                            rows="3"
                                            id="details"
                                            placeholder="Additional info"
                                        ></textarea>
                                        <span v-if="errors.body" class="error">{{ errors.body }}</span>
                                    </div>
                                </div>
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="due_date">Due Date:</label>
                                    </div>
                                    <div class="col-9">
                                        <input
                                            @focus="clearError('dueDate')  "
                                            v-model="todo.dueDate"
                                            type="date"
                                            class="form-control"
                                            id="due_date"
                                        >
                                        <span v-if="errors.dueDate" class="error">{{ errors.dueDate }}</span>
                                    </div>
                                </div>
                                <div v-if="shouldShowReminder" class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="remind_at">Remind me:</label>
                                    </div>
                                    <div class="col-9">
                                        <select @change="clearError('remindAt')" v-model="todo.remindAt" id="remind_at" class="form-control">
                                            <option value="0" disabled selected>Never</option>
                                            <option value="1">1 day before due date</option>
                                            <option value="2">2 days before due date</option>
                                            <option value="3">3 days before due date</option>
                                            <option value="4">4 days before due date</option>
                                            <option value="5">5 days before due date</option>
                                            <option value="6">6 days before due date</option>
                                            <option value="7">1 week before due date</option>
                                            <option value="14">2 weeks before due date</option>
                                            <option value="21">3 weeks before due date</option>
                                            <option value="28">4 weeks before due date</option>
                                        </select>
                                        <span v-if="errors.remindAt" class="error">{{ errors.remindAt }}</span>
                                    </div>
                                </div>
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="image">Image:</label>
                                    </div>
                                    <div class="col-9">
                                        <input @change="selectFile('image')" @focus="clearError('image')" type="file" id="image" accept="image/*">
                                        <span v-if="errors.image" class="error">{{ errors.image }}</span>
                                    </div>
                                </div>
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="file">Attachment:</label>
                                    </div>
                                    <div class="col-9">
                                        <input @change="selectFile('file')" type="file" id="file">
                                        <span v-if="errors.attachment" class="error">{{ errors.attachment }}</span>
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
                        @click="submitToDo"
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
import moment from '../../../../node_modules/moment';

const STORE_TO_DO_ROUTE = '/api/store-to-do';

export default {
    props: {
        isActive: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            todo: {
                title: '',
                body: '',
                dueDate: null,
                remindAt: 0,
                image: null,
                attachment: null,
            },
            errors: {
                title: '',
                body: '',
                dueDate: '',
                remindAt: '',
                image: '',
                attachment: '',
            }
        }
    },
    computed: {
        shouldShowReminder() {
            const today = moment().format('YYYY-MM-DD');
            return this.todo.dueDate &&
                this.todo.dueDate > today;
        }
    },
    methods: {
        clearError(error) {
            this.errors[error] = '';
        },
        clearAllErrors() {
            const errorObj = this.errors;
            for (const key of Object.keys(errorObj)) {
                this.errors[key] = '';
            }
        },
        closeModal() {
            EventBus.$emit('close-modal');
        },
        reminderIsInTheFuture() {
            return moment().add(1, 'days').format('YYYY-MM-DD') <=
                moment(this.todo.dueDate).subtract(this.todo.remindAt, 'days').format('YYYY-MM-DD');
        },
        selectFile(type) {
            if (type === 'image') {
                this.todo.image = event.target.files[0];
            }

            if (type === 'file') {
                this.todo.attachment = event.target.files[0];
            }
        },
        submitToDo() {
            if (this.validateData()) {
                const formData = new FormData();

                Object.entries(this.todo).forEach(
                    part => {
                        if (part[1]) formData.append(part[0], part[1])
                    }
                );

                axios.post(STORE_TO_DO_ROUTE, formData)
                    .then(response => {

                    })
                    .catch(errors => {
                        const errorObj = errors.response.data.errors;
                        for (const [key, value] of Object.entries(errorObj)) {
                            this.errors[key] = value[0];
                        }
                    })
            }
        },
        validateData() {
            let isValid = true;
            this.clearAllErrors();

            if (this.todo.title.length < 2) {
                this.errors.title = 'The title must be at least two characters';
                isValid = false;
            }

            if (this.todo.dueDate) {
                if (this.todo.dueDate < this.today) {
                    this.errors.dueDate = 'The due date cannot be in the past';
                    isValid = false;
                }
            }

            if (this.todo.remindAt) {
                  if (! this.reminderIsInTheFuture()) {
                    this.errors.remindAt = 'Reminders cannot be set in the past';
                    isValid = false;
                }
            }

            return isValid;
        }
    }
}
</script>

<style lang="scss" scoped>
.label {
    font-weight: bold;
    padding-top: 7px;
}
</style>