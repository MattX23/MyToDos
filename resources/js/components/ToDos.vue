<template>
    <div class="col-sm-6">
        <header>{{ headerText }}
            <span
                v-if="!complete"
                @click="openInputToDoModal()"
                class="drop-shadow"
                id="addToDo"
            >+</span>
        </header>
        <div class="row">
            <div class="col-12 todo-container">
                <div v-if="!toDos.length" class="empty-container">{{ emptyContainerText }}</div>
                <div v-else v-for="toDo in toDos">
                    <article
                        :key="toDo.id"
                        class="todo-item"
                    >
                        <div class="row">
                            <div class="col-10">
                                <span id="todo-header">
                                    <input v-if="!complete" @click="toggleToDoStatus(toDo.id, true)" type="checkbox">
                                    <span id="todo-header-text" :class="{ 'strike-through' : complete }">{{ toDo.title }}</span>
                                </span>
                                <div v-if="!complete" class="row">
                                    <div class="d-none d-lg-block col-6" v-if="toDo.due_date">
                                        <div
                                            :class="[ isOverDue(toDo.due_date) ? 'btn-danger inactive-btn-danger ' : 'btn-warning inactive-btn-warning' ]"
                                            class="btn btn-pill drop-shadow margin-left"
                                            title="Due date"
                                        ><i class="zmdi zmdi-calendar"></i> {{ toDo.due_date }}</div>
                                    </div>
                                    <div class="d-none d-lg-block col-6" v-if="toDo.remind_at">
                                        <div
                                            class="btn btn-pill btn-info inactive-btn-info drop-shadow float-left"
                                            title="Reminder set"
                                        ><i class="zmdi zmdi-time"></i> {{ toDo.remind_at.substr(0, 10) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="toDo.image" class="col-2">
                                <img class="todo-image round-image" :src="toDo.image" alt="">
                            </div>
                            <div v-else class="col-2">
                                <div class="image-placeholder round-image">{{ toDo.title.substring(0, 1).toUpperCase() }}</div>
                            </div>
                        </div>
                        <div class="col-12 toolbar-row">
                            <button
                                @click="openViewToDoModal(toDo)"
                                class="btn btn-sm btn-round btn-secondary"
                                title="View To Do"
                            >
                                <i class="zmdi zmdi-eye"></i>
                            </button>
                            <button
                                v-if="!complete"
                                @click="openInputToDoModal(toDo)"
                                class="btn btn-sm btn-round btn-secondary"
                                title="Edit To Do"
                            >
                                <i class="zmdi zmdi-edit"></i>
                            </button>
                            <button
                                @click="openDeleteToDoModal(toDo)"
                                class="btn btn-sm btn-round btn-secondary"
                                title="Delete To Do"
                            >
                                <i class="zmdi zmdi-delete"></i>
                            </button>
                            <button
                                v-if="complete"
                                @click="toggleToDoStatus(toDo.id, false)"
                                class="btn btn-sm btn-round btn-secondary"
                                title="Add to In Progress"
                            >
                                <i class="zmdi zmdi-refresh"></i>
                            </button>
                            <span v-if="hasAttachment(toDo)" class="has-attachment">
                                    <i class="zmdi zmdi-attachment-alt"></i>
                            </span>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { EventBus } from "../eventbus/event-bus";
import moment from '../../../node_modules/moment';

const TOGGLE_TO_DO_ROUTE = '/api/toggle-to-do/';

export default {
    props: {
        complete: {
            type: Boolean,
            default: false,
        },
        toDos: {
            type: Array,
            default: null
        },
        userId: {
            type: Number,
        }
    },
    computed: {
        emptyContainerText() {
            return !this.$props.complete ?
                '🎉 You\'re all caught up!' :
                'You have no completed To Dos';
        },
        headerText() {
            return !this.$props.complete ?
                `In Progress (${this.$props.toDos.length})` :
                `Complete (${this.$props.toDos.length})`;
        }
    },
    methods: {
        hasAttachment(toDo) {
            return toDo.attachment;
        },
        isOverDue(dueDate) {
            return moment(dueDate).isBefore();
        },
        openDeleteToDoModal(toDo) {
            EventBus.$emit('modal-open-delete-todo', toDo);
        },
        openInputToDoModal(toDo = null) {
            EventBus.$emit('modal-open-manage-todo', toDo);
        },
        openViewToDoModal(toDo) {
            EventBus.$emit('modal-open-view-todo', toDo);
        },
        toggleToDoStatus(toDoId, isComplete) {
            const data = {
                complete: isComplete
            };

            axios.post(`${TOGGLE_TO_DO_ROUTE}${toDoId}/${this.$props.userId}`, data)
                .then(response => {
                    EventBus.$emit('update-todos', response.data);
                    const message = isComplete ? '✅ Nice! To Do completed!' : '🔄 To Do marked as In Progress!';
                    EventBus.$emit('show-flash-message', message, 'success');
                })
                .catch(() => EventBus.$emit('show-flash-message', '⚠️ Something went wrong', 'danger'))
        }
    }
}
</script>

<style lang="scss" scoped>
@import '../../sass/variables';

.empty-container {
    font-size: 1.5rem;
    text-align: center;
}
.todo-container {
    background: $white-semi-transparent;
    padding-bottom: 30px;
    height: 80vh;
    max-height: 80vh;
    overflow: scroll;
    padding-top: 10px;
}
.todo-item {
    background: $white;
    padding: 5px;
    margin-bottom: 10px;
    min-height: 120px;
}
#todo-header {
    font-size: $font-size-md;
    font-weight: bold;
}
header {
    color: $white;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
#todo-dates {
    display: flex;
    justify-content: space-around;
}
.toolbar-row {
    display: flex;
    justify-content: space-around;
    background: lightgray;
    position: relative;
    top: 1px;
    margin-top: 10px;
    padding: 5px;
}
.todo-image{
    float: right;
}
.due-date {
    font-size: 1.5rem;
    position: relative;
    top: 4px;
}
#todo-header-text {
    margin-left:10px;
}
#addToDo {
    float: right;
    background: #52B56E;
    color: white;
    width: 40px;
    text-align: center;
    height: 40px;
    margin-top: 7px;
    cursor: pointer;
    border-radius: 10px;
    font-size: 1.5rem;
}
.has-attachment {
    font-size: 1.2rem;
    position: relative;
    top: 2px;
}
</style>
