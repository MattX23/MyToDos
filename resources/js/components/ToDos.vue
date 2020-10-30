<template>
    <div class="col-sm-6">
        <header>In Progress
            <span
                    @click="openAddToDoModal"
                    id="addToDo"
                    class="drop-shadow"
            >+</span>
        </header>
        <div class="row">
            <div class="col-12 todo-container">
                <div v-for="todo in todos">
                    <article
                            :key="todo.id"
                            class="todo-item"
                    >
                        <div class="row">
                            <div class="col-10">
                        <span id="todo-header">
                            <input type="checkbox">
                            <span id="todo-header-text">{{ todo.title }}</span>
                        </span>
                                <div class="row">
                                    <div class="col-6" v-if="todo.due_date">
                                        <div class="btn btn-pill btn-warning inactive-btn-warning drop-shadow float-right" title="Due date"><i class="zmdi zmdi-calendar"></i> {{ todo.due_date }}</div>
                                    </div>
                                    <div class="col-6" v-if="todo.remind_at">
                                        <div class="btn btn-pill btn-info inactive-btn-info drop-shadow float-left" title="Reminder set"><i class="zmdi zmdi-time"></i> {{ todo.remind_at }}</div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="todo.image" class="col-2">
                                <img class="todo-image" :src="todo.image" alt="">
                            </div>
                        </div>
                        <div class="toolbar">
                            <div class="toolbar-row">
                                <button class="btn btn-sm btn-round btn-secondary">
                                    <i class="zmdi zmdi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-secondary btn-round">
                                    <i class="zmdi zmdi-attachment-alt"></i>
                                </button>
                                <button :class="[ hasReminder(todo) ? 'btn-secondary' : 'btn-secondary-dim' ]" class="btn btn-sm btn-secondary btn-round">
                                    <i class="zmdi zmdi-time"></i>
                                </button>
                                <button class="btn btn-sm btn-round btn-secondary">
                                    <i class="zmdi zmdi-delete"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { EventBus } from "../eventbus/event-bus";

export default {
    props: {
        todos: {
            type: Array,
            default: null
        }
    },
    data() {
        return {

        }
    },
    methods: {
        hasReminder(todo) {
            return todo.remind_at;
        },
        openAddToDoModal() {
            EventBus.$emit('modal-open-add-todo');
        }
    }
}
</script>

<style lang="scss" scoped>
@import '../../sass/variables';

.todo-container {
    background: $white-semi-transparent;
    padding-bottom: 30px;
    max-height: 80vh;
    overflow: scroll;
    padding-top: 10px;
}
.todo-item {
    position: relative;
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
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
.toolbar {
    position: absolute;
    bottom: 3px;
    width: 100%;
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
    right: 5px;
    top: 1px;
    padding-top: 5px;
    padding-bottom: 5px;
}
.todo-image{
    width: 65px;
    border-radius: 100px;
    float: right;
    height: 65px;
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
</style>
