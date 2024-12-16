<template>
  <PageHeader
    sticky
    :title="$t('craftable-pro', 'Edit [[modelName]]')"
    :subtitle="`Last updated at ${dayjs(
      [[modelNameLowerCase]].updated_at
    ).format('DD.MM.YYYY')}`"
  >
    <Button
      :leftIcon="ArrowDownTrayIcon"
      @click="submit"
      :loading="form.processing"
      v-can="'craftable-pro.[[modelPermissionName]].edit'"
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
import type { [[modelIndexName]], [[modelIndexName]]Form } from "./types";
import dayjs from "dayjs";
import customParseFormat from "dayjs/plugin/customParseFormat";
[[editVueImports]]

dayjs.extend(customParseFormat);

[[translatableFunctionality]]

interface Props {
  [[modelNameLowerCase]]: [[modelIndexName]];
  [[relationsProps]]
}

const props = defineProps<Props>();

const { form, submit } = useForm<[[modelIndexName]]Form>(
    {
          [[editFormColumns]]
    },
    route("[[modelUpdateRoute]]", [props.[[modelNameLowerCase]]?.id])
);
</script>
