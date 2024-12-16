<template>
  <li class="flex gap-x-3">
    <div class="flex">
      <div class="w-[32px] h-[32px] text-base rounded-full border-gray-300 font-bold text-gray-900 border-solid border flex items-center justify-center">
        {{ order }}
      </div>
    </div>
    <div>
      <div class="text-slate-900 font-semibold leading-5">
        <slot name="title"></slot>
      </div>
      <div class="text-slate-500 leading-5">
        <slot name="content"></slot>
      </div>
    </div>
  </li>
</template>

<script setup lang="ts">

interface Props {
  order: string
}

const props = defineProps<Props>();

</script>
