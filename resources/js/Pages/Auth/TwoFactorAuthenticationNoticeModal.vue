<template>
  <Modal :open="open" @toggle-open="toggleOpen"  :external-open="true" alignButtons="right" size="md">
    <template #content>
      <div class="mt-6 flex flex-col gap-5 items-center">
        <img src="/craftable-pro/images/qr-code-crossmark.svg" width="104" height="104" alt="qr code icon"/>
        <div>
          <div class="text-lg text-slate-900 font-semibold leading-8 items-center text-center">
            {{ $t('craftable-pro', 'Please set up your 2FA now')}}
          </div>
          <div class="text-sm text-slate-500 font-light">
            {{ $t('craftable-pro', 'Increase your account’s security by enabling Two-Factor Authentication (2FA).')}}
          </div>
        </div>
        <Alert type="warning" >
          {{ $t('craftable-pro', 'The global settings require you to enable Two-Factor authentication for your account.') }}
        </Alert>
      </div>
    </template>
    <template #buttons="{ setIsOpen }" class="gap-1">
      <Button
        variant="outline"
        color="gray"
        @click="
          () => {
            setIsOpen();
          }
        "
      >
        {{ $t("craftable-pro", "Configure it later") }}
      </Button>
      <Button @click="
          () => {
            setIsOpen();
            router.visit(route('craftable-pro.craftable-pro-users.profile') + '?tab=1');
          }
        " color="success">
        {{ $t("craftable-pro", "Set up Two-Factor Authentication") }}
      </Button>
    </template>
  </Modal>
</template>

<script lang="ts" setup>
import {Alert, Button, Modal} from "craftable-pro/Components";
import {router} from "@inertiajs/vue3";

interface Props {
  open: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits(["toggleOpen"]);

const toggleOpen = () => {
  emit("toggleOpen");
};
</script>
