<template>
  <div>
    <Head :title="$t('craftable-pro', 'Login')"/>

    <div
      class="bg-white shadow sm:rounded-lg py-12 px-6" style="padding-bottom: 24px"
      v-auto-animate
    >
      <div class="w-full flex flex-col items-center align-center justify-center gap-5">
        <template v-if="recoveryCodeLogin">
          <img src="/craftable-pro/images/2fa-recovery-code.svg" width="207" height="62" alt="recovery code icon"/>
          <div class="flex flex-col w-full">
            <div class="text-slate-900 text-lg font-semibold">
              {{ $t('craftable-pro', 'Use a recovery code') }}
            </div>
            <div class="text-slate-500 text-sm">
              {{ $t('craftable-pro', 'Enter your single-use code from your recovery codes list.') }}
            </div>
          </div>

          <form class="flex flex-col space-y-3 w-full" @submit.prevent="submit">
            <TextInput
              v-model="form.recovery_code"
              :placeholder="$t('craftable-pro', 'Recovery code')"
              name="code"
              type="text"
            />

            <Button class="w-full" type="submit" :disabled="form.processing">
              {{ $t("craftable-pro", "Verify") }}
            </Button>
            <Button class="w-full" type="button" color="gray" variant="outline" @click="recoveryCodeLogin=false">
              {{ $t("craftable-pro", "Back") }}
            </Button>
          </form>
        </template>
        <template v-else>
          <img src="/craftable-pro/images/2fa-phone.svg" width="86" height="92" alt="phone icon"/>
          <div class="flex flex-col w-full">
            <div class="text-slate-900 text-lg font-semibold">
              {{ $t('craftable-pro', '2-Step Verification') }}
            </div>
            <div class="text-slate-500 text-sm">
              {{ $t('craftable-pro', 'Enter 6-digit verification code from the authentication app') }}
            </div>
          </div>

          <form class="flex flex-col space-y-3 w-full" @submit.prevent="submit">
            <TextInput
              v-model="form.code"
              :placeholder="$t('craftable-pro', 'Enter code')"
              name="code"
              type="text"
            />

            <Button class="w-full" type="submit" :disabled="form.processing">
              {{ $t("craftable-pro", "Verify") }}
            </Button>
          </form>
          <div class="w-full flex flex-col">
            <div class="text-slate-900 text-sm font-semibold">
              {{ $t('craftable-pro', 'Don’t have your phone?') }}
            </div>
            <div>
              <a class="text-indigo-600 text-sm" href="#" @click="recoveryCodeLogin = true">{{ $t('craftable-pro', 'Use a recovery code to access your account.') }}</a>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {useForm, Head, router} from "@inertiajs/vue3";
import {
  Button,
  TextInput,
} from "craftable-pro/Components";
import { useToast } from "@brackets/vue-toastification";
import axios from "axios";
import {ref} from "vue";

interface Props {
}

defineProps<Props>();
const toast = useToast();

const form = useForm({
  code: "",
  recovery_code: "",
});

const recoveryCodeLogin = ref(false);

const submit = () => {
  const data = recoveryCodeLogin.value === true
    ? { recovery_code: form.recovery_code }
    : { code: form.code };

  axios.post(route("two-factor.login"), data).then((response: any) => {
      router.replace('/admin/login'); // BE will take care of correct routing after login
    })
    .catch((error) =>
      toast.error(error.response.data.message)
    );
}
</script>
