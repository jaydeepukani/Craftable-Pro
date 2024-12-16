<template>
  <div class="mt-6 flex flex-col gap-5">
    <OrderedList>
      <OrderedListItem order="1">
        <template #title>
          {{ $t('craftable-pro', 'Open an authentication app') }}
        </template>
        <template #content>
          {{ $t('craftable-pro', 'We recommend downloading') }}
          <a target="_blank" href="https://apps.apple.com/us/app/google-authenticator/id388497605" class="text-indigo-600">{{ $t('craftable-pro', 'Google Authenticator') }}</a>
          {{ $t('craftable-pro', 'if you do not have one installed.') }}
        </template>
      </OrderedListItem>
      <OrderedListItem order="2">
        <template #title>
          {{ $t('craftable-pro', 'Scan this QR code or copy the key') }}
        </template>
        <template #content>
          {{ $t('craftable-pro', 'Use the app to scan QR code or manually enter the following code: ') }}
          <div class="text-slate-700">
            {{ props.twoFactorAuthenticationSetupCode }}
            <Tooltip position="top" class="inline-flex" :useHover="false">
              <template #button>
                <a class="text-indigo-600 px-2" href="#" @click="copyKeySuccess = copyKey(twoFactorAuthenticationSetupCode)">{{ $t('craftable-pro', 'Copy Key') }}</a>
              </template>
              <template #content>
                {{ copyKeySuccess ? $t('craftable-pro', 'Copied!') : $t('craftable-pro', 'Could not copy to clipboard!') }}
              </template>
            </Tooltip>
          </div>
        </template>
      </OrderedListItem>
    </OrderedList>
  </div>
  <div class="flex gap-5 align-center justify-center items-center mt-8 mb-4">
    <div class="border-4 border-slate-300 w-[160px] h-[160px] flex align-center justify-center items-center" id="two-fa-setup-qr">
      <span v-html="props.twoFactorAuthenticationSetupQrSvg"></span>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from "vue";
import OrderedList from "craftable-pro/Components/OrderedList.vue";
import OrderedListItem from "craftable-pro/Components/OrderedListItem.vue";
import {Tooltip} from "craftable-pro/Components";
import {handleTwoFactorAuthenticationActions} from "craftable-pro/hooks/useTwoFactorRecoveryCodesActions";

interface Props {
  processing: boolean;
  twoFactorAuthenticationSetupCode: string,
  twoFactorAuthenticationSetupQrSvg: string,
}

const {
  copyKey
} = handleTwoFactorAuthenticationActions();

const props = defineProps<Props>();

const copyKeySuccess = ref(false);

</script>
