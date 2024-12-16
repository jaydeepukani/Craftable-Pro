<template>
  <Card :title="$t('craftable-pro', 'Two factor authentication (2FA)')">
    <div class="grid grid-cols-6 gap-6">
      <div class="flex gap-1 col-span-12 items-center" style="position: relative;">
        <div class="w-1/3 text-sm font-medium text-gray-700">
          {{ $t('craftable-pro', 'Authentication app')}}
        </div>
        <div class="w-1/3 text-sm">
          <div v-if="twoFactorAuthenticationEnabledAt" class="flex items-center">
            <CheckCircleIconOutline
              class="h-6 w-6 text-success-400 stroke-2 mr-1"
            />
            {{ $t('craftable-pro', 'Enabled') }}
            <span class="text-slate-500 ml-2">{{ twoFactorAuthenticationEnabledAt }}</span>
          </div>
          <div v-else class="flex align-center items-center">
            <ExclamationCircleIconOutline
              class="h-6 w-6 text-danger-400 stroke-2 mr-1"
            />
            {{ $t('craftable-pro', 'Disabled')}}
          </div>
        </div>
        <div class="w-1/3 text-right" v-if="!twoFactorAuthenticationEnabledAt">
          <Button @click="twoFactorAuthenticationSetupModalOpened = true;" :leftIcon="PlusIcon">
            {{ $t("craftable-pro", "Set up authentication app") }}
          </Button>
          <TwoFactorAuthenticationSetupModal
            @toggle-open="twoFactorAuthenticationSetupModalOpened = !twoFactorAuthenticationSetupModalOpened"
            :open="twoFactorAuthenticationSetupModalOpened"
          />
        </div>
        <div class="w-1/3 text-right" v-else>
          <Button @click="twoFactorAuthenticationManageModalOpened = true;" variant="outline" color="gray">
            {{ $t("craftable-pro", "Manage app") }}
          </Button>
          <TwoFactorAuthenticationManageModal
            @toggle-open="twoFactorAuthenticationManageModalOpened = !twoFactorAuthenticationManageModalOpened"
            :open="twoFactorAuthenticationManageModalOpened"
            :two-factor-authentication-enabled-at="twoFactorAuthenticationEnabledAt"
            :change-app-clicked="() => {alert('Hello')}"
          />
        </div>
      </div>
    </div>
  </Card>
</template>

<script setup lang="ts">
import {
  Button,
  Card,
} from "craftable-pro/Components";
import {
  CheckCircleIcon as CheckCircleIconOutline,
  ExclamationCircleIcon as ExclamationCircleIconOutline,
} from "@heroicons/vue/24/outline";
import {PlusIcon} from "@heroicons/vue/20/solid";
import {ref} from "vue";
import TwoFactorAuthenticationSetupModal from "craftable-pro/Pages/CraftableProUser/Components/TwoFactorAuthenticationSetupModal.vue";
import TwoFactorAuthenticationManageModal from "craftable-pro/Pages/CraftableProUser/Components/TwoFactorAuthenticationManageModal.vue";

interface Props {
  twoFactorAuthenticationEnabledAt: string;
}
const twoFactorAuthenticationSetupModalOpened = ref<boolean>(false);
const twoFactorAuthenticationManageModalOpened = ref<boolean>(false);

const props = defineProps<Props>();
</script>
