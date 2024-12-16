<template>
  <Card :title="$t('craftable-pro', 'Password')">
    <div class="grid grid-cols-6 gap-6">
      <TextInput
        v-model="form.password"
        name="password"
        :label="$t('craftable-pro', 'Password') + ' *'"
        type="password"
        autocomplete="new-password"
        class="col-span-12"
        labelPlacement="left"
      />
      <TextInput
        v-model="form.password_confirmation"
        name="password_confirmation"
        :label="$t('craftable-pro', 'Password confirmation') + ' *'"
        type="password"
        autocomplete="new-password"
        class="col-span-12"
        labelPlacement="left"
      />
      <div class="flex gap-1 col-span-12 grid grid-cols-12">
        <Button
          class="flex gap-1 col-span-3 col-start-5"
          @click="submit"
          :loading="form.processing"
        >
          {{ $t("craftable-pro", "Save password") }}
        </Button>
      </div>
    </div>
  </Card>
</template>

<script setup lang="ts">
import {
  Button,
  Card,
  TextInput,
} from "craftable-pro/Components";
import {useForm} from "../../../hooks/useForm";

const { form, submit } = useForm(
  {
    password: "",
    password_confirmation: "",
  },
  route("craftable-pro.craftable-pro-users.password.update")
);

</script>
