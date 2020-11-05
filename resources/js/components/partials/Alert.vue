<template>
    <div
        id="alert"
        :class="[ showAlert ? 'slide' : null, appliedClass ]"
        class="alert"
    >
        {{ message }}
    </div>
</template>

<script>

import { EventBus } from "../../eventbus/event-bus";
import { TWO_SECOND_TIMEOUT } from "../../helpers/constants";


export default {
    props: {
        level: {
            type: String,
            default: null,
        },
        message: {
            type: String,
            default: null,
        },
        showAlert: {
            type: Boolean,
            default: false,
        }
    },
    computed: {
        appliedClass: function() {
            return `alert-${this.level}`;
        }
    },
    watch: {
        showAlert: function (val) {
            if (val) this.toggleAlert();
        }
    },
    methods: {
        toggleAlert() {
            const alert = document.getElementById('alert');

            alert.classList.remove('slide');

            setTimeout(function() {
                EventBus.$emit('hide-flash-message');
            }, TWO_SECOND_TIMEOUT)
        },
    }
}
</script>

<style lang="scss" scoped>
@import '../../../sass/variables';

.alert {
    background: $white;
    border-radius: 0;
    color: $grey-dark;
    height: 5rem;
    padding-top: 1.65rem;
    position: fixed;
    right: -20rem;
    text-align: center;
    top: 86%;
    transition: 0.5s;
    width: 20rem;
    z-index: 9999;
    font-size: 1rem;
}
.alert-danger {
    border: 1px solid $red;
}
.alert-success {
    border: 1px solid $green;
}
.alert-warning {
    border: 1px solid $orange;
}
.slide {
    right: 0;
    transition: 0.5s;
}

</style>
