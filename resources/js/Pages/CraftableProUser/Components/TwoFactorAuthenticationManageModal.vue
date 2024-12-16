<template>
  <Modal :open="open" @toggle-open="toggleOpen" :external-open="true" alignButtons="right" size="md">
    <template #title v-if="currentStep === 1">
      {{ $t("craftable-pro", "Manage authentication app") }}</template
    >
    <template #title v-if="currentStep <= 3 && !deleteTwoFactorAuth">
      {{ $t("craftable-pro", "Change authentication app") }}</template
    >
    <template #title v-if="currentStep === 2 && deleteTwoFactorAuth">
      {{ $t("craftable-pro", "Remove authentication app") }}</template
    >
    <template #content>
      <TwoFactorAuthenticationManageModalFirstStep
        v-if="currentStep === 1"
        :two-factor-authentication-enabled-at="twoFactorAuthenticationEnabledAt"
        @change-app-clicked="nextStep"
        @remove-app-clicked="removeTwoFactorAuthClicked"
      />
      <TwoFactorAuthenticationRemoveModal
        v-if="currentStep === 2 && deleteTwoFactorAuth"
        :two-factor-authentication-enabled-at="twoFactorAuthenticationEnabledAt"
      />
      <TwoFactorAuthenticationManageModalSecondStep
        v-if="currentStep === 2 && !deleteTwoFactorAuth"
        :processing="processing"
        :two-factor-authentication-enabled-at="twoFactorAuthenticationEnabledAt"
        :two-factor-authentication-setup-code="state.twoFactorAuthenticationSetupCode"
        :two-factor-authentication-setup-qr-svg="state.twoFactorAuthenticationSetupQrSvg"
      />
      <TwoFactorAuthenticationSetupVerificationModal
        v-if="currentStep === 3"
        :processing="processing"
        @input-confirmation-code="(code) => (state.twoFactorAuthenticationConfirmationCode = code)"
      />
      <TwoFactorAuthenticationSetupRecoveryCodesModal
        v-if="currentStep === 4"
        :processing="processing"
        :two-factor-authentication-recovery-codes="state.twoFactorAuthenticationRecoveryCodes"
      />
    </template>
    <template #buttons="{ setIsOpen }" class="gap-1">
      <Button
        v-if="currentStep !== 4"
        variant="outline"
        color="gray"
        @click="
          () => {
            currentStep = 1;
            setIsOpen();
          }
        "
      >
        {{ $t("craftable-pro", "Cancel") }}
      </Button>
      <Button
        v-if="currentStep === 2 && !deleteTwoFactorAuth"
        :disabled="processing"
        @click="nextStep"
        left-icon-custom-style="margin-right: 0.25rem"
      >
        {{ $t("craftable-pro", "Next step") }}
      </Button>
      <Button
        v-if="currentStep === 2 && deleteTwoFactorAuth"
        :disabled="processing"
        @click="removeTwoFactorAuthConfirmed"
        left-icon-custom-style="margin-right: 0.25rem"
      >
        {{ $t("craftable-pro", "Confirm removal") }}
      </Button>
      <Button
        v-else-if="currentStep === 3"
        :disabled="processing"
        @click="nextStep"
        left-icon-custom-style="margin-right: 0.25rem"
      >
        {{ $t("craftable-pro", "Verify") }}
      </Button>
      <Tooltip position="top" class="inline-flex" :useHover="false" v-if="currentStep === 4">
        <template #button>
          <Button
            :left-icon="ClipboardIcon"
            @click="copyRecoveryCodesSuccess = copyRecoveryCodes(state.twoFactorAuthenticationRecoveryCodes.join('\r\n'))"
            left-icon-custom-style="margin-right: 0.25rem"
          >
            {{ $t("craftable-pro", "Copy codes") }}
          </Button>
        </template>
        <template #content>
          {{ copyRecoveryCodesSuccess ? $t('craftable-pro', 'Copied!') : $t('craftable-pro', 'Could not copy to clipboard!') }}
        </template>
      </Tooltip>
      <Button
        v-if="currentStep === 4"
        @click="downloadRecoveryCodes(state.twoFactorAuthenticationRecoveryCodes.join('\r\n'))"
        :left-icon="ArrowDownTrayIcon"
        left-icon-custom-style="margin-right: 0.25rem"
      >
        {{ $t("craftable-pro", "Download codes") }}
      </Button>
      <Button
        v-if="currentStep === 4"
        @click="printRecoveryCodes(state.twoFactorAuthenticationRecoveryCodes.join('<br/>'))"
        :left-icon="PrinterIcon"
        left-icon-custom-style="margin-right: 0.25rem"
      >
        {{ $t("craftable-pro", "Print codes") }}
      </Button>
      <Button
        v-if="currentStep === 4"
        variant="outline"
        color="gray"
        left-icon-custom-style="margin-right: 0.25rem"
        @click="
          () => {
            setIsOpen();
            router.reload();
            currentStep = 1;
          }
        "
      >
        {{ $t("craftable-pro", "Close") }}
      </Button>
    </template>
  </Modal>
</template>

<script lang="ts" setup>
import {Button, Modal, Tooltip} from "craftable-pro/Components";
import {reactive, ref} from "vue";
import axios from "axios";
import { useToast } from "@brackets/vue-toastification";
import { router } from "@inertiajs/vue3";
import {ClipboardIcon, PrinterIcon} from "@heroicons/vue/24/solid";
import {ArrowDownTrayIcon} from "@heroicons/vue/20/solid";
import {trans} from "craftable-pro/plugins/laravel-vue-i18n";
import TwoFactorAuthenticationManageModalFirstStep
  from "craftable-pro/Pages/CraftableProUser/Components/TwoFactorAuthenticationManageModalFirstStep.vue";
import TwoFactorAuthenticationRemoveModal
  from "craftable-pro/Pages/CraftableProUser/Components/TwoFactorAuthenticationRemoveModal.vue";
import TwoFactorAuthenticationManageModalSecondStep
  from "craftable-pro/Pages/CraftableProUser/Components/TwoFactorAuthenticationManageModalSecondStep.vue";
import TwoFactorAuthenticationSetupVerificationModal
  from "craftable-pro/Pages/CraftableProUser/Components/TwoFactorAuthenticationSetupVerificationModal.vue";
import TwoFactorAuthenticationSetupRecoveryCodesModal
  from "craftable-pro/Pages/CraftableProUser/Components/TwoFactorAuthenticationSetupRecoveryCodesModal.vue";
import {
  handleTwoFactorAuthenticationActions
} from "craftable-pro/hooks/useTwoFactorRecoveryCodesActions";

interface Props {
  open: boolean;
  twoFactorAuthenticationEnabledAt: string;
  changeAppClicked: Function;
}

const props = defineProps<Props>();

const processing = ref<boolean>(false);
const currentStep = ref<number>(1);
const deleteTwoFactorAuth = ref<boolean>(false);

const toast = useToast();
const copyRecoveryCodesSuccess = ref(false);

const emit = defineEmits(["toggleOpen"]);

const toggleOpen = () => {
  emit("toggleOpen");
};

const removeTwoFactorAuthClicked = () => {
  deleteTwoFactorAuth.value = true;
  currentStep.value = 2;
};

const nextStep = async () => {
  processing.value = true;

  if (currentStep.value === 1) {
    await loadTwoFactorAuthenticationRefreshData();
    currentStep.value = 2;
    processing.value = false;
  } else if (currentStep.value === 2) {
    currentStep.value = 3;
    processing.value = false;
  } else if (currentStep.value === 3) {
    axios
      .post(route('craftable-pro.settings.update-new-2FA-codes'), {code: state.twoFactorAuthenticationConfirmationCode})
      .then(
        (response) => {
          currentStep.value = 4;
        },
        (error) => {
          toast.error(error.response.data.message ?? trans("craftable-pro", "An error has occurred."));
        }
      )
      .finally(() => {
        processing.value = false;
      });
  }
};

const state = reactive({
  twoFactorAuthenticationSetupCode: "",
  twoFactorAuthenticationConfirmationCode: "",
  twoFactorAuthenticationSetupQrSvg: "",
  twoFactorAuthenticationRecoveryCodes: [],
});

const {
  printRecoveryCodes,
  copyRecoveryCodes,
  downloadRecoveryCodes
} = handleTwoFactorAuthenticationActions();

async function loadTwoFactorAuthenticationRefreshData() {
  await axios.get(route("craftable-pro.settings.new-2FA-codes"), {})
    .then((response: any) => {
      state.twoFactorAuthenticationSetupCode = response.data.twoFactorAuthenticationSetupCode;
      state.twoFactorAuthenticationSetupQrSvg = response.data.twoFactorAuthenticationSetupQrSvg;
      state.twoFactorAuthenticationRecoveryCodes = response.data.twoFactorAuthenticationRecoveryCodes;
    })
    .catch((error) =>
      toast.error(trans("craftable-pro", "An error occurred loading the 2FA data."))
    );
}

function removeTwoFactorAuthConfirmed() {
  axios.delete(route("two-factor.disable"), {})
    .then((response: any) => {
      router.reload();
      toast.info(trans("craftable-pro", "Two factor authentication successfully disabled."));
    })
    .catch((error) =>
      toast.error(trans("craftable-pro", "An error occurred while disabling the 2FA."))
    );
}

</script>
