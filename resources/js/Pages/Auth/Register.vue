<template>
  <div>
    <Head :title="$t('craftable-pro', 'Register')" />

    <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
      <form class="space-y-6" @submit.prevent="submit">
        <TextInput
          v-model="form.first_name"
          :label="$t('craftable-pro', 'First name')"
          name="first_name"
        />

        <TextInput
          v-model="form.last_name"
          :label="$t('craftable-pro', 'Last name')"
          name="last_name"
        />

        <TextInput
          v-model="form.email"
          :label="$t('craftable-pro', 'E-mail address')"
          name="email"
        />

        <TextInput
          v-model="form.password"
          :label="$t('craftable-pro', 'Password')"
          name="password"
          type="password"
          autocomplete="current-password"
        />

        <TextInput
          v-model="form.password_confirmation"
          :label="$t('craftable-pro', 'Confirm Password')"
          name="password_confirmation"
          type="password"
          autocomplete="new-password"
        />

        <SelectInput
            :options="availableLocales"
            v-model="form.locale"
            label="Locale"
            name="locale"
        />

        <div class="flex items-center justify-end">
          <div class="text-sm">
            <Link
              :href="route('craftable-pro.login')"
              class="font-medium text-primary-600 hover:text-primary-500"
            >
              {{ $t("craftable-pro", "Already registered?") }}
            </Link>
          </div>
        </div>

        <Button class="w-full" type="submit" :disabled="form.processing">
          {{ $t("craftable-pro", "Register") }}
        </Button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm, Head } from "@inertiajs/vue3";
import { Button, TextInput } from "craftable-pro/Components";
import SelectInput from "craftable-pro/Components/SelectInput.vue";
import { useToast } from "@brackets/vue-toastification";
import { useFormLocale } from "craftable-pro/hooks/useFormLocale";

const toast = useToast();
const { availableLocales, defaultLocale } = useFormLocale();

const form = useForm({
  first_name: "",
  last_name: "",
  email: "",
  password: "",
  password_confirmation: "",
  locale: defaultLocale,
});

const submit = () => {
  form.post(route("craftable-pro.register"), {
    onFinish: () => form.reset("password", "password_confirmation"),
    onError: (errors: Errors) => {
      if (errors && Object.values(errors)) {
        toast.error(Object.values(errors)[0]);
      }
    }
  });
};
</script>
