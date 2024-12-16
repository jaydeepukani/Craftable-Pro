<template>
  <div class="mt-6 flex flex-col gap-5 items-center">
    <img src="/craftable-pro/images/qr-code-checkmark.svg" width="104" height="104" alt="qr code icon"/>
    <div>
      <div class="text-lg text-slate-900 font-semibold leading-8 items-center text-center">
        {{ $t('craftable-pro', '2FA is enabled')}}
      </div>
      <div class="text-sm text-slate-500 font-light">
        {{ $t('craftable-pro', 'You’ll get a login code from your authentication app.')}}
      </div>
    </div>
    <Alert type="info" >
      {{ $t('craftable-pro', 'Please copy, download, or print your recovery codes before proceeding.') }}
    </Alert>
    <div class="rounded-md border border-slate-300 flex flex-col gap-4 p-4 w-full">
      <div class="flex flex-col gap-2 pb-4 border-b border-slate-200">
        <div class="text-slate-900 text-sm font-semibold">
          {{ $t('craftable-pro', 'Your recovery codes') }}
        </div>
        <div class="text-slate-500 text-sm">
          {{ $t('craftable-pro', 'If you lose your phone each of these recovery codes can be used one time each to regain access to your account. Please save them in a safe place, or you will lose access to your account.') }}
        </div>
      </div>
      <div class="flex gap-5 grid grid-cols-2">
        <div v-for="(code, index) in props.twoFactorAuthenticationRecoveryCodes" class="text-slate-900 rounded-full py-2 text-center w-full bg-slate-100 text-sm">
          {{ code }}
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>

import { Alert } from "craftable-pro/Components";

interface Props {
  twoFactorAuthenticationRecoveryCodes: [string];
}

const props = defineProps<Props>();
</script>
<style>

</style>
