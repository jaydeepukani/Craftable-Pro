<template>
  <Modal :open="open" @toggle-open="toggleOpen" :external-open="true" alignButtons="right" size="md">
    <template #title v-if="currentStep !== 3">
      {{ $t("craftable-pro", "Set up authentication app") }}</template
    >
    <template #content>
      <TwoFactorAuthenticationSetupModalFirstStep
        v-if="currentStep === 1"
        :processing="processing"
        :two-factor-authentication-setup-code="state.twoFactorAuthenticationSetupCode"
        :two-factor-authentication-setup-qr-svg="state.twoFactorAuthenticationSetupQrSvg"
      />
      <TwoFactorAuthenticationSetupVerificationModal
        v-if="currentStep === 2"
        :processing="processing"
        @input-confirmation-code="(code) => (state.twoFactorAuthenticationConfirmationCode = code)"
      />
      <TwoFactorAuthenticationSetupRecoveryCodesModal
        v-if="currentStep === 3"
        :processing="processing"
        :two-factor-authentication-recovery-codes="state.twoFactorAuthenticationRecoveryCodes"
      />
    </template>
    <template #buttons="{ setIsOpen }" class="gap-1">
      <Button
        v-if="currentStep !== 3"
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
        v-if="currentStep === 1"
        :disabled="processing"
        @click="nextStep"
        left-icon-custom-style="margin-right: 0.25rem"
      >
        {{ $t("craftable-pro", "Next step") }}
      </Button>
      <Button
        v-else-if="currentStep === 2"
        :disabled="processing"
        @click="nextStep"
        left-icon-custom-style="margin-right: 0.25rem"
      >
        {{ $t("craftable-pro", "Verify") }}
      </Button>
      <Tooltip position="top" class="inline-flex" :useHover="false" v-if="currentStep === 3">
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
        v-if="currentStep === 3"
        @click="downloadRecoveryCodes(state.twoFactorAuthenticationRecoveryCodes.join('\r\n'))"
        :left-icon="ArrowDownTrayIcon"
        left-icon-custom-style="margin-right: 0.25rem"
      >
        {{ $t("craftable-pro", "Download codes") }}
      </Button>
      <Button
        v-if="currentStep === 3"
        @click="printRecoveryCodes(state.twoFactorAuthenticationRecoveryCodes.join('<br\>'))"
        :left-icon="PrinterIcon"
        left-icon-custom-style="margin-right: 0.25rem"
      >
        {{ $t("craftable-pro", "Print codes") }}
      </Button>
      <Button
        v-if="currentStep === 3"
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
import {onMounted, reactive, ref} from "vue";
import axios from "axios";
import { useToast } from "@brackets/vue-toastification";
import { router } from "@inertiajs/vue3";
import TwoFactorAuthenticationSetupModalFirstStep from "./TwoFactorAuthenticationSetupModalFirstStep.vue";
import {ClipboardIcon, PrinterIcon} from "@heroicons/vue/24/solid";
import {ArrowDownTrayIcon} from "@heroicons/vue/20/solid";
import {trans} from "craftable-pro/plugins/laravel-vue-i18n";
import TwoFactorAuthenticationSetupVerificationModal from "craftable-pro/Pages/CraftableProUser/Components/TwoFactorAuthenticationSetupVerificationModal.vue";
import TwoFactorAuthenticationSetupRecoveryCodesModal from "craftable-pro/Pages/CraftableProUser/Components/TwoFactorAuthenticationSetupRecoveryCodesModal.vue";
import {
  handleTwoFactorAuthenticationActions
} from "craftable-pro/hooks/useTwoFactorRecoveryCodesActions";


interface Props {
  open: boolean;
}

const props = defineProps<Props>();

const processing = ref<boolean>(false);
const currentStep = ref<number>(1);

const toast = useToast();
const copyRecoveryCodesSuccess = ref(false);

const emit = defineEmits(["toggleOpen"]);

const toggleOpen = () => {
  emit("toggleOpen");
};

const {
  printRecoveryCodes,
  copyRecoveryCodes,
  downloadRecoveryCodes
} = handleTwoFactorAuthenticationActions();

const nextStep = () => {
  processing.value = true;

  if (currentStep.value === 1) {
    currentStep.value = 2;
    processing.value = false;
  } else if (currentStep.value === 2) {
    axios
      .post(route('two-factor.confirm'), {code: state.twoFactorAuthenticationConfirmationCode})
      .then(
        (response) => {
          currentStep.value = 3;
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

onMounted(() => {
  axios.post(route("two-factor.enable"), {})
    .then((response: any) => {
      loadTwoFactorAuthenticationSetupData();
    })
    .catch((error) =>
      toast.error(trans("craftable-pro", "An error occurred loading the 2FA data."))
    );
})

async function loadTwoFactorAuthenticationSetupData() {
  await axios.get(route("craftable-pro.settings.2FA-codes"), {})
    .then((response: any) => {
      state.twoFactorAuthenticationSetupCode = response.data.twoFactorAuthenticationSetupCode;
      state.twoFactorAuthenticationSetupQrSvg = response.data.twoFactorAuthenticationSetupQrSvg;
      state.twoFactorAuthenticationRecoveryCodes = response.data.twoFactorAuthenticationRecoveryCodes;
    })
    .catch((error) =>
      toast.error(trans("craftable-pro", "An error occurred loading the 2FA data."))
    );
}
</script>
