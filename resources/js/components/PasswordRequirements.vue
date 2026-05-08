<template>
  <div class="mt-2 space-y-1">
    <div 
      v-for="(req, index) in requirements" 
      :key="index"
      class="flex items-center text-xs transition-colors duration-300"
      :class="req.met ? 'text-green-600' : 'text-red-500'"
    >
      <svg 
        class="h-3 w-3 mr-1.5" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke="currentColor"
      >
        <path 
          v-if="req.met" 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          stroke-width="3" 
          d="M5 13l4 4L19 7" 
        />
        <path 
          v-else 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          stroke-width="3" 
          d="M6 18L18 6M6 6l12 12" 
        />
      </svg>
      {{ req.label }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  password: {
    type: String,
    required: true,
    default: ''
  }
});

const requirements = computed(() => [
  {
    label: 'Mínimo 8 caracteres',
    met: props.password.length >= 8
  },
  {
    label: 'Pelo menos 1 letra maiúscula',
    met: /[A-Z]/.test(props.password)
  },
  {
    label: 'Pelo menos 1 letra minúscula',
    met: /[a-z]/.test(props.password)
  },
  {
    label: 'Pelo menos 1 caractere especial (!@#$%^&*)',
    met: /[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]/.test(props.password)
  }
]);
</script>
