<template>
  <div class="relative" ref="containerRef">
    <div
      class="min-h-[38px] w-full rounded-md border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-600 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm dark:text-gray-200"
      :class="{ 'ring-1 ring-indigo-500 border-indigo-500': isOpen }"
      @click="toggleDropdown"
    >
      <div class="flex flex-wrap gap-1">
        <!-- Tags dos itens selecionados -->
        <div
          v-for="item in selectedItems"
          :key="item.value"
          class="inline-flex items-center gap-1 rounded-full bg-indigo-100 dark:bg-indigo-900 px-2 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-200"
        >
          {{ item.label }}
          <button
            type="button"
            class="ml-1 inline-flex h-4 w-4 items-center justify-center rounded-full hover:bg-indigo-200 dark:hover:bg-indigo-800"
            @click.stop="removeItem(item)"
          >
            <span class="sr-only">Remover {{ item.label }}</span>
            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
              <path
                fill-rule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>

        <!-- Input de busca -->
        <input
          ref="searchInput"
          type="text"
          class="flex-1 min-w-[120px] border-0 bg-transparent p-0 text-sm text-gray-900 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-0"
          :placeholder="selectedItems.length ? '' : placeholder"
          v-model="search"
          @input="filterOptions"
          @keydown="handleKeydown"
        />
      </div>
    </div>

    <!-- Dropdown com opções -->
    <div
      v-show="isOpen"
      class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white dark:bg-gray-800 py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
    >
      <div
        v-if="filteredOptions.length === 0"
        class="relative cursor-default select-none py-2 px-3 text-gray-700 dark:text-gray-300"
      >
        Nenhuma opção encontrada
      </div>
      <div
        v-for="option in filteredOptions"
        :key="option.value"
        class="relative cursor-pointer select-none py-2 px-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/50"
        :class="{ 'bg-indigo-50 dark:bg-indigo-900/50': isSelected(option) }"
        @click="toggleOption(option)"
      >
        <div class="flex items-center">
          <input
            type="checkbox"
            :checked="isSelected(option)"
            class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700"
            @click.stop
            @change="toggleOption(option)"
          />
          <span class="ml-2 block truncate dark:text-gray-200">{{ option.label }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  options: {
    type: Array,
    required: true,
    validator: (value) => {
      return value.every(option => 'value' in option && 'label' in option)
    }
  },
  placeholder: {
    type: String,
    default: 'Selecione as opções...'
  }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const search = ref('')
const searchInput = ref(null)
const containerRef = ref(null)
const selectedItems = ref([])

const filteredOptions = computed(() => {
  if (!search.value) return props.options
  const searchLower = search.value.toLowerCase()
  return props.options.filter(option =>
    option.label.toLowerCase().includes(searchLower)
  )
})

const handleClickOutside = (event) => {
  if (containerRef.value && !containerRef.value.contains(event.target)) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    nextTick(() => {
      searchInput.value?.focus()
    })
  }
}

const closeDropdown = () => {
  isOpen.value = false
  search.value = ''
}

const isSelected = (option) => {
  return selectedItems.value.some(item => item.value === option.value)
}

const toggleOption = (option) => {
  const index = selectedItems.value.findIndex(item => item.value === option.value)
  if (index === -1) {
    selectedItems.value.push(option)
  } else {
    selectedItems.value.splice(index, 1)
  }
  emit('update:modelValue', selectedItems.value.map(item => item.value))
}

const removeItem = (item) => {
  const index = selectedItems.value.findIndex(selected => selected.value === item.value)
  if (index !== -1) {
    selectedItems.value.splice(index, 1)
    emit('update:modelValue', selectedItems.value.map(item => item.value))
  }
}

const handleKeydown = (event) => {
  if (event.key === 'Escape') {
    closeDropdown()
  }
}

watch(() => props.modelValue, (newValue) => {
  selectedItems.value = props.options.filter(option =>
    newValue.includes(option.value)
  )
}, { immediate: true })

watch(selectedItems, (newValue) => {
  if (newValue.length === props.options.length) {
    closeDropdown()
  }
})
</script>

<style scoped>
.v-click-outside {
  position: relative;
}
</style>
