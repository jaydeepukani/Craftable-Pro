<template>
  <PageHeader sticky :title="$t('craftable-pro', 'User Settings')">
  </PageHeader>

  <PageContent>
    <template #tabs>
      <TabGroup variant="underline" >
        <Tab>
          {{ $t("craftable-pro", "Profile") }}
        </Tab>
        <Tab>
          <div class="flex items-center gap-3">
            {{ $t("craftable-pro", "Security") }}
          </div>
        </Tab>
      </TabGroup>
    </template>

    <TabPanel>
      <ProfileCard :form="form" :submit="submit" />
    </TabPanel>
    <TabPanel>
      <div class="mx-auto flex max-w-3xl flex-col gap-6 2xl:max-w-4xl">
        <ChangePasswordCard>
        </ChangePasswordCard>
        <TwoFactorAuthenticationCard
          :two-factor-authentication-enabled-at="craftableProUser.two_factor_confirmed_at ? dayjs(craftableProUser.twoFactorAuthenticationEnabledAt).format('YYYY-MM-DD HH:mm:ss') : null"
        >
        </TwoFactorAuthenticationCard>
      </div>
    </TabPanel>
  </PageContent>
</template>

<script setup lang="ts">
import {
  PageHeader,
  PageContent,
  Tab,
  TabGroup,
} from "craftable-pro/Components";
import { useForm } from "craftable-pro/hooks/useForm";
import {TabPanel} from "@headlessui/vue";
import ProfileCard from "craftable-pro/Pages/CraftableProUser/Components/ProfileCard.vue";
import type {CraftableProUser} from "craftable-pro/types/models";
import ChangePasswordCard from "craftable-pro/Pages/CraftableProUser/Components/ChangePasswordCard.vue";
import TwoFactorAuthenticationCard from "craftable-pro/Pages/CraftableProUser/Components/TwoFactorAuthenticationCard.vue";
import dayjs from "dayjs";

interface Props {
  craftableProUser: CraftableProUser;
}

const props = defineProps<Props>();

const { form, submit } = useForm(
  {
    first_name: props.craftableProUser.first_name ?? "",
    last_name: props.craftableProUser.last_name ?? "",
    email: props.craftableProUser.email ?? "",
    locale: props.craftableProUser.locale ?? "",
    avatar: props.craftableProUser.avatar ?? [],
  },
  route("craftable-pro.craftable-pro-users.profile.update")
);
</script>
