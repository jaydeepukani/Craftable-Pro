<template>
  <Dropdown noContentPadding :zIndex="50" contentClass="w-[calc(100vw-5rem)] sm:w-auto">
    <template #button>
      <IconButton
        :icon="EllipsisVerticalIcon"
        variant="ghost"
        color="gray"
        rounded
      />
    </template>

    <template #content>
      <div class="divide-y divide-gray-100">
        <UserDropdownContent />
        <div class="py-1">
          <MenuItem v-slot="{ active }">
            <form :action="route('craftable-pro.logout')" method="post">
              <input type="hidden" name="_token" :value="csrfToken" />
              <button
                type="submit"
                :class="[
                  active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                  'block w-full px-4 py-2 text-left text-sm',
                ]"
              >
                {{ $t("craftable-pro", "Sign out") }}
              </button>
            </form>
          </MenuItem>
        </div>
      </div>
    </template>
  </Dropdown>
</template>

<script setup lang="ts">
import { MenuItem } from "@headlessui/vue";
import { EllipsisVerticalIcon } from "@heroicons/vue/24/solid";
import { Dropdown, IconButton } from "./index";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { PageProps } from "../types/page";
import UserDropdownContent from "@/craftable-pro/Components/UserDropdown.vue";

const csrfToken = computed(() => (usePage().props as PageProps).csrf_token);
</script>
