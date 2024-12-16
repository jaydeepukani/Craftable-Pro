<template>
  <SlickList axis="y" v-model:list="items" useDragHandle>
    <SlickItem
      v-for="(item, i) in items"
      :key="item.id"
      :index="i"
      class="bg-gray-100 mb-4 flex rounded-md border p-3 shadow-sm"
    >
      <div class="grid w-full grid-cols-1">
        <div class="flex items-center gap-5">
          <DragHandle
            class="flex place-items-center hover:cursor-grab active:cursor-grabbing"
          >
            <Bars3Icon class="h-5 w-5" />
          </DragHandle>

          <slot name="label" :item="item">
            {{ item }}
          </slot>
        </div>
      </div>

      <div class="flex content-center gap-2 pl-8 text-right">
        <slot name="actions" :item="item" />

        <div v-if="showDelete">
          <IconButton
            :icon="XMarkIcon"
            as="button"
            variant="outline"
            color="danger"
            size="sm"
            @click="removeItem(item.id)"
          />
        </div>
      </div>
    </SlickItem>
  </SlickList>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { SlickList, SlickItem, DragHandle } from "vue-slicksort";
import { IconButton } from "craftable-pro/Components";
import { XMarkIcon, Bars3Icon } from "@heroicons/vue/24/outline";

interface Props {
  modelValue: any;
  showDelete?: boolean;
  orderValueProp?: string;
}

const props = withDefaults(defineProps<Props>(), {
  showDelete: false,
  orderValueProp: "order",
});

const emit = defineEmits(["update:modelValue"]);

const items = computed({
  get: () => {
    return props.modelValue;
  },
  set: (value) => {
    let newValueOrdered = value.map((element: any, index: number) => {
      return (element = {
        ...element,
        [props.orderValueProp]: index + 1,
      });
    });

    emit("update:modelValue", newValueOrdered);
  },
});

const removeItem = (itemId: Number) => {
  const newItems = items.value.filter((item) => item.id !== itemId);
  items.value = newItems;
};
</script>
