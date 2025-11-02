<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { CheckCircleIcon, XCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const page = usePage()
const show = ref(false)
const message = ref('')
const type = ref('success')

watch(() => page.props.flash, (flash) => {
    if (flash.success) {
        showMessage(flash.success, 'success')
    } else if (flash.error) {
        showMessage(flash.error, 'error')
    }
}, { deep: true })

const showMessage = (msg, msgType) => {
    message.value = msg
    type.value = msgType
    show.value = true

    setTimeout(() => {
        show.value = false
    }, 5000)
}

const close = () => {
    show.value = false
}
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-2 opacity-0"
        >
            <div
                v-if="show"
                class="fixed top-4 right-4 z-50 max-w-md w-full"
            >
                <div
                    :class="[
                        'rounded-lg shadow-lg p-4 flex items-start space-x-3',
                        type === 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'
                    ]"
                >
                    <CheckCircleIcon
                        v-if="type === 'success'"
                        class="h-6 w-6 text-green-600 flex-shrink-0"
                    />
                    <XCircleIcon
                        v-else
                        class="h-6 w-6 text-red-600 flex-shrink-0"
                    />
                    <p
                        :class="[
                            'flex-1 text-sm font-medium',
                            type === 'success' ? 'text-green-800' : 'text-red-800'
                        ]"
                    >
                        {{ message }}
                    </p>
                    <button
                        @click="close"
                        :class="[
                            'flex-shrink-0 p-1 rounded hover:bg-opacity-20',
                            type === 'success' ? 'hover:bg-green-600' : 'hover:bg-red-600'
                        ]"
                    >
                        <XMarkIcon class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
