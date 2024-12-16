<template>
  <PageHeader sticky :title="$t('craftable-pro', 'Create [[modelName]]')">
    <Button
      :leftIcon="ArrowDownTrayIcon"
      @click="submit"
      :loading="form.processing"
      v-can="'craftable-pro.[[modelPermissionName]].create'"
    >
      {{ $t("craftable-pro", "Save") }}
    </Button>
  </PageHeader>

  <Form :form="form" :submit="submit" [[relationsFormProps]] />
</template>

<script setup lang="ts">
import { ArrowDownTrayIcon } from "@heroicons/vue/24/outline";
import { PageHeader, Button } from "craftable-pro/Components";
import { useForm } from "craftable-pro/hooks/useForm";
import Form from "./Form.vue";
import type { [[modelIndexName]]Form } from "./types";
import dayjs from 'dayjs'

[[translatableFunctionality]]

interface Props {
  [[relationsProps]]
}

const props = defineProps<Props>();

const { form, submit } = useForm<[[modelIndexName]]Form>(
    {
          [[createFormColumns]]
    },
    route("[[modelStoreRoute]]"),
    "post"
);
</script>
