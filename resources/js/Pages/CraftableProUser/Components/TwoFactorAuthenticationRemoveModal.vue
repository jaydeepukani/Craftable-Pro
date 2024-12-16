<template>
  <div class="mt-6 flex flex-col gap-5">
    <div class="flex flex-col gap-2">
      <div class="text-slate-500 text-sm">
        {{ $t('craftable-pro', 'You will no longer be able to use your old verification app:') }}
      </div>
      <div class="rounded-md border border-slate-200 flex flex-col gap-2 p-3.5 w-full">
        <div class="flex gap-2 items-center">
          <img src="/craftable-pro/images/qr-code-icon.svg" width="24" height="24" alt="qr code icon"/>
          <div class="text-slate-800 text-sm">
            {{ $t('craftable-pro', 'Authenticator') }}
          </div>
          <div class="text-slate-500 text-sm">
            {{ $t('craftable-pro', 'Added') + ": " + twoFactorAuthenticationEnabledAt }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>

interface Props {
  twoFactorAuthenticationEnabledAt: string;
}

const props = defineProps<Props>();

</script>
