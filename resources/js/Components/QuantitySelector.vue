<script setup>
import { MinusIcon, PlusIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    modelValue: Number,
    min: {
        type: Number,
        default: 1
    },
    max: {
        type: Number,
        default: 999
    }
})

const emit = defineEmits(['update:modelValue'])

const increment = () => {
    if (props.modelValue < props.max) {
        emit('update:modelValue', props.modelValue + 1)
    }
}

const decrement = () => {
    if (props.modelValue > props.min) {
        emit('update:modelValue', props.modelValue - 1)
    }
}
</script>

<template>
    <div class="flex items-center space-x-3">
        <button
            @click="decrement"
            :disabled="modelValue <= min"
            class="btn-secondary btn-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <MinusIcon class="h-4 w-4" />
        </button>
        <input
            :value="modelValue"
            @input="$emit('update:modelValue', parseInt($event.target.value))"
            type="number"
            :min="min"
            :max="max"
            class="w-16 text-center input-field"
        />
        <button
            @click="increment"
            :disabled="modelValue >= max"
            class="btn-secondary btn-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <PlusIcon class="h-4 w-4" />
        </button>
    </div>
</template>
