<template>
    <div
        :class="{ 'is-active' : isActive }"
        aria-hidden="true"
        class="modal"
        id="input-modal"
        role="dialog"
        tabindex="-1"
    >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ modalTitle }}</h5>
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
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="title">Title:</label>
                                    </div>
                                    <div class="col-9">
                                        <input
                                            @keydown="clearError('title')"
                                            v-model="toDo.title"
                                            class="form-control"
                                            id="title"
                                            placeholder="What do you need to do?"
                                            type="text"
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
                                            v-model="toDo.body"
                                            class="form-control"
                                            id="details"
                                            placeholder="Additional info"
                                            rows="3"
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
                                            @focus="clearError('dueDate')"
                                            v-model="toDo.dueDate"
                                            class="form-control"
                                            id="due_date"
                                            type="date"
                                        >
                                        <span v-if="errors.dueDate" class="error">{{ errors.dueDate }}</span>
                                    </div>
                                </div>
                                <div v-if="shouldShowReminder" class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="remind_at">Remind me:</label>
                                    </div>
                                    <div class="col-9">
                                        <input
                                            @focus="clearError('remindAt')"
                                            v-model="toDo.remindAt"
                                            class="form-control"
                                            id="remind_at"
                                            type="date"
                                        >
                                        <span v-if="errors.remindAt" class="error">{{ errors.remindAt }}</span>
                                    </div>
                                </div>
                                <div v-if="toDo.remindAt" class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label>At:</label>
                                    </div>
                                    <div class="col-9">
                                        <select
                                            v-model="toDo.remindAtTime"
                                            class="form-control"
                                        >
                                            <option v-for="(_, hour) in 24" :value="hour" :key="hour">{{ hour }}:00 {{ getTimeOfDay(hour) }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div v-if="showImageField && activeToDo.image" class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="image">Image:</label>
                                    </div>
                                    <div v-if="activeToDo.image" class="col-7 margin-btm-sm">
                                        <img :src="activeToDo.image" class="todo-image round-image modal-header-image" alt="image">
                                    </div>
                                    <div class="col-2">
                                        <span
                                            @click="removeImage()"
                                            class="remove-btn"
                                        >X</span>
                                    </div>
                                </div>
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="image">{{ imageLabel }}</label>
                                    </div>
                                    <div class="col-9">
                                        <input
                                            @change="selectFile('image')"
                                            @focus="clearError('image')"
                                            accept="image/*"
                                            id="image"
                                            type="file"
                                        >
                                        <span v-if="errors.image" class="error">{{ errors.image }}</span>
                                    </div>
                                </div>
                                <div v-if="showAttachmentField && activeToDo.attachment" class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="attachment">Attachment:</label>
                                    </div>
                                    <div class="col-7">
                                        <p id="attachment">
                                            <a
                                                :href="activeToDo.attachment.file_path"
                                                target="_blank"
                                                title="Download"
                                            >
                                                {{ activeToDo.attachment.display_name }}
                                            </a>
                                        </p>
                                    </div>
                                    <div class="col-2">
                                        <span
                                            @click="removeAttachment()"
                                            class="remove-btn"
                                        >X</span>
                                    </div>
                                </div>
                                <div class="row margin-btm-sm">
                                    <div class="col-3 label">
                                        <label for="file">{{ attachmentLabel }}</label>
                                    </div>
                                    <div class="col-9">
                                        <input
                                            @change="selectFile('file')"
                                            id="file"
                                            type="file"
                                        >
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
                        {{ submitButtonText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { EventBus } from "../../eventbus/event-bus";
import moment from '../../../../node_modules/moment';

const STORE_TO_DO_ROUTE = '/api/store-to-do/';
const EDIT_TO_DO_ROUTE = '/api/edit-to-do/';
const REMIND_AT_DEFAULT_TIME = 8;

export default {
    props: {
        activeToDo: {
            type: Object,
            default: null,
        },
        isActive: {
            type: Boolean,
            default: false
        },
        userId: {
            type: Number,
        }
    },
    data() {
        return {
            isEditing: false,
            errors: {
                attachment: '',
                body: '',
                dueDate: '',
                image: '',
                remindAt: '',
                remindAtTime: '',
                title: '',
            },
            reminderDays: {},
            showAttachmentField: false,
            showImageField: false,
            toDo: {
                attachment: null,
                body: '',
                deleteAttachment: false,
                deleteImage: false,
                dueDate: null,
                id: null,
                image: null,
                remindAt: "",
                remindAtTime: REMIND_AT_DEFAULT_TIME,
                title: '',
            },
        }
    },
    computed: {
        attachmentLabel() {
            return this.isEditing && this.activeToDo.attachment ? "Change:" : "Attachment";
        },
        dueDate() {
            return this.toDo.dueDate;
        },
        imageLabel() {
            return this.isEditing && this.activeToDo.image ? "Change:" : "Image:";
        },
        modalTitle() {
            return this.isEditing ? "Edit To Do" : "Create a To Do";
        },
        shouldShowReminder() {
            const today = moment().format('YYYY-MM-DD');

            return (this.toDo.dueDate && this.toDo.dueDate > today) ||
                (this.activeToDo && this.activeToDo.remind_at);
        },
        submitButtonText() {
            return this.isEditing ? "Edit" : "Create";
        }
    },
    watch: {
        activeToDo: function(val) {
            if (val) {
                this.isEditing = true;
                this.showAttachmentField = true;
                this.showImageField = true;
                this.toDo.body = this.$props.activeToDo.body;
                this.toDo.dueDate = this.$props.activeToDo.due_date;
                this.toDo.existingAttachment = this.$props.activeToDo.attachment ?
                    this.$props.activeToDo.attachment.display_name :
                    {};
                this.toDo.existingImage = this.$props.activeToDo.image;
                this.toDo.id = this.$props.activeToDo.id;
                this.toDo.remindAt = this.$props.activeToDo.remind_at;
                this.toDo.remindAtTime = this.toDo.remindAt ?
                    this.$props.activeToDo.remind_at_time :
                    REMIND_AT_DEFAULT_TIME;
                this.toDo.title = this.$props.activeToDo.title;
            } else {
                this.isEditing = false;
                this.showAttachmentField = false;
                this.showImageField = false;
                this.toDo.remindAtTime = REMIND_AT_DEFAULT_TIME;
            }
        },
        dueDate: function (val) {
            if (val === null || val === "") this.toDo.remindAt = "";
        },
        isActive: function (val) {
            if (val && !this.activeToDo) this.toDo.remindAtTime = REMIND_AT_DEFAULT_TIME;
        },
    },
    methods: {
        clearAllErrors() {
            const errorObj = this.errors;
            for (const key of Object.keys(errorObj)) {
                this.errors[key] = '';
            }
        },
        clearError(error) {
            this.errors[error] = '';
        },
        clearFields() {
            const fieldObj = this.toDo;
            for (const key of Object.keys(fieldObj)) {
                this.toDo[key] = '';
            }
        },
        closeModal() {
            this.clearFields();
            this.clearAllErrors();
            EventBus.$emit('close-modal');
        },
        getTimeOfDay(hour) {
            return hour < 12 ? 'AM' : 'PM';
        },
        reminderIsBeforeDueDate() {
            return moment(this.toDo.dueDate).format('YYYY-MM-DD') >
                moment(this.toDo.remindAt).format('YYYY-MM-DD');
        },
        reminderIsInTheFuture() {
            return moment().add(1, 'days').format('YYYY-MM-DD') <=
                moment(this.toDo.remindAt).format('YYYY-MM-DD');
        },
        removeAttachment() {
            this.showAttachmentField = false;
            this.toDo.deleteAttachment = true;
        },
        removeImage() {
            this.showImageField = false;
            this.toDo.deleteImage = true;
        },
        selectFile(type) {
            if (type === 'image') {
                this.toDo.image = event.target.files[0];
            }

            if (type === 'file') {
                this.toDo.attachment = event.target.files[0];
            }
        },
        submitToDo() {
            if (this.validateData()) {
                const formData = new FormData();

                Object.entries(this.toDo).forEach(
                    part => {
                        if (part[1]) formData.append(part[0], part[1])
                    }
                );

                let route = STORE_TO_DO_ROUTE;

                if (this.isEditing) {
                    route = `${EDIT_TO_DO_ROUTE}${this.toDo.id}/`;
                    formData.append('_method', 'PUT')
                }

                axios.post(`${route}${this.$props.userId}`, formData)
                    .then(response => {
                        this.closeModal();
                        EventBus.$emit('update-todos', response.data);
                        const message = !this.isEditing ? '💾 To Do saved!' : '✏️ To Do updated!';
                        EventBus.$emit('show-flash-message', message, 'success');
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

            if (this.toDo.title.length < 2) {
                this.errors.title = 'The title must be at least two characters';
                isValid = false;
            }

            if (this.toDo.dueDate) {
                if (this.toDo.dueDate < this.today) {
                    this.errors.dueDate = 'The due date cannot be in the past';
                    isValid = false;
                }
            }

            if (this.toDo.remindAt) {
                if (! this.reminderIsInTheFuture()) {
                    this.errors.remindAt = 'Reminders cannot be set in the past';
                    isValid = false;
                }

                if (! this.reminderIsBeforeDueDate()) {
                    this.errors.remindAt = 'The Reminder date must be before the Due Date';
                    isValid = false;
                }
            }

            return isValid;
        }
    }
}
</script>

<style lang="scss" scoped>
@import '../../../sass/variables';

.label {
    font-weight: bold;
    padding-top: 7px;
}
.remove-btn {
    background: $grey;
    color: $white;
    padding: 4px 8px;
    border-radius: 10px;
    float: right;
    cursor: pointer;
    position: relative;
    top: 5px;
    font-size: 0.65rem;
}
.remove-btn:hover {
    background: $red;
}
</style>