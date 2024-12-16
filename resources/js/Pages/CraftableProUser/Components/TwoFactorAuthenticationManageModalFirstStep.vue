<template>
  <div class="mt-6 flex flex-col gap-5">
    <div class="text-slate-500 text-sm">
      {{ $t('craftable-pro', 'You’ll get a login code from your authentication app. We will now ask for a login code anytime you log in on a device we do not recognize.') }}
    </div>
    <div class="rounded-md border border-slate-200 flex flex-col gap-2 p-3.5 w-full">
      <div class="text-slate-900 font-semibold">
        {{ $t('craftable-pro', 'Your authentication app') }}
      </div>
      <div class="flex gap-2 items-center">
        <img src="/craftable-pro/images/qr-code-icon.svg" width="24" height="24" alt="qr code icon"/>
        <div class="text-slate-800 text-sm">
          {{ $t('craftable-pro', 'Authenticator') }}
        </div>
        <div class="text-slate-500 text-sm">
          {{ $t('craftable-pro', 'Added') + ": " + twoFactorAuthenticationEnabledAt }}
        </div>
        <div class="text-right flex gap-4 ml-auto items-center">
          <a class="text-indigo-600" href="#" @click="$emit('changeAppClicked')">{{ $t('craftable-pro', 'Change app') }}</a>
          <a :title="$t('craftable-pro', 'Remove app')" href="#" @click="$emit('removeAppClicked')"><TrashIcon class="w-[20px]" @click="$emit('removeAppClicked')"></TrashIcon></a>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>

import {
  TrashIcon,
} from "@heroicons/vue/20/solid";

interface Props {
  twoFactorAuthenticationEnabledAt: string;
}

const emit = defineEmits(["changeAppClicked", "removeAppClicked"]);

const props = defineProps<Props>();

</script>
