<template>
  <div>
    <Head :title="$t('craftable-pro', 'Login')" />

    <div
      class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10"
      v-auto-animate
    >
      <Alert v-if="status" type="info" class="mb-6">
        {{ status }}
      </Alert>

      <form class="space-y-6" @submit.prevent="submit">
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

        <div class="flex items-center justify-between">
          <Checkbox
            v-model="form.remember"
            :label="$t('craftable-pro', 'Remember me')"
            name="remember-me"
          />

          <div v-if="canResetPassword" class="text-sm">
            <Link
              :href="route('craftable-pro.password.request')"
              class="font-medium text-primary-600 hover:text-primary-500"
            >
              {{ $t("craftable-pro", "Forgot your password?") }}
            </Link>
          </div>
        </div>

        <Button class="w-full" type="submit" :disabled="form.processing">
          {{ $t("craftable-pro", "Sign in") }}
        </Button>

        <hr
          v-if="
            Object.values($page.props.config?.socialite).filter(
              (value) => value === true
            ).length > 0
          "
        />

        <Button
          as="a"
          class="font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center mr-2 w-full justify-center"
          :href="route('craftable-pro.social-login.login', 'microsoft')"
          v-if="$page.props.config?.socialite?.microsoft"
        >
          <MicrosoftLoginLogo class="mr-2" />
          {{ $t("craftable-pro", "Microsoft login") }}
        </Button>

        <Button
          as="a"
          class="w-full justify-center text-white bg-[#1da1f2] hover:bg-[#1da1f2]/90 focus:ring-4 focus:ring-[#1da1f2]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#1da1f2]/55 mr-2 !mt-2"
          :href="route('craftable-pro.social-login.login', 'twitter')"
          v-if="$page.props.config?.socialite?.twitter"
        >
          <TwitterLoginLogo class="mr-2" />
          {{ $t("craftable-pro", "Sign in with Twitter") }}
        </Button>

        <Button
          as="a"
          class="w-full justify-center text-white bg-[#24292F] hover:bg-[#24292F]/90 focus:ring-4 focus:ring-[#24292F]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-gray-500 dark:hover:bg-[#050708]/30 mr-2 !mt-2"
          :href="route('craftable-pro.social-login.login', 'github')"
          v-if="$page.props.config?.socialite?.github"
        >
          <GithubLoginLogo class="mr-2" />
          {{ $t("craftable-pro", "Sign in with Github") }}
        </Button>
        <Button
          as="a"
          class="w-full justify-center text-white bg-[#4285F4] hover:bg-[#4285F4]/90 focus:ring-4 focus:ring-[#4285F4]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#4285F4]/55 mr-2 !mt-2"
          :href="route('craftable-pro.social-login.login', 'google')"
          v-if="$page.props.config?.socialite?.google"
        >
          <GoogleLoginLogo class="mr-2" />
          {{ $t("craftable-pro", "Sign in with Google") }}
        </Button>
        <Button
          as="a"
          class="w-full justify-center text-white bg-[#3b5998] hover:bg-[#3b5998]/90 focus:ring-4 focus:ring-[#3b5998]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#3b5998]/55 mr-2 !mt-2"
          :href="route('craftable-pro.social-login.login', 'facebook')"
          v-if="$page.props.config?.socialite?.facebook"
        >
          <FacebookLoginLogo class="mr-2" />
          {{ $t("craftable-pro", "Sign in with Facebook") }}
        </Button>
        <Button
          as="a"
          class="w-full justify-center text-white bg-[#050708] hover:bg-[#050708]/90 focus:ring-4 focus:ring-[#050708]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:focus:ring-[#050708]/50 dark:hover:bg-[#050708]/30 mr-2 !mt-2"
          :href="route('craftable-pro.social-login.login', 'apple')"
          v-if="$page.props.config?.socialite?.apple"
        >
          <AppleLoginLogo class="mr-2" />
          {{ $t("craftable-pro", "Sign in with Apple") }}
        </Button>

        <div v-if="canRegister" class="text-sm text-center">
          {{ $t("craftable-pro", "Not registered?") }}
          <Link
            :href="route('craftable-pro.register')"
            class="font-medium text-primary-600 hover:text-primary-500"
          >
            {{ $t("craftable-pro", "Create an account") }}
          </Link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm, Head } from "@inertiajs/vue3";
import {
  Button,
  TextInput,
  Checkbox,
  Alert,
  MicrosoftLoginLogo,
  TwitterLoginLogo,
  FacebookLoginLogo,
  GithubLoginLogo,
  AppleLoginLogo,
  GoogleLoginLogo,
} from "craftable-pro/Components";
import { useToast } from "@brackets/vue-toastification";

interface Props {
  canResetPassword: boolean;
  canRegister: boolean;
  status: string;
}

defineProps<Props>();
const toast = useToast();

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const submit = () => {
  form.post(route("craftable-pro.login"), {
    onFinish: () => form.reset("password"),
    onError: (errors: Error) => {
      if (errors && Object.values(errors)) {
        toast.error(Object.values(errors)[0]);
      }
    },
  });
};
</script>
