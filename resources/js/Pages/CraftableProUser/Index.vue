<template>
  <PageHeader :title="$t('craftable-pro', 'Users')">
    <Modal alignButtons="right" size="sm">
      <template #trigger="{ setIsOpen }">
        <Button @click="() => setIsOpen(true)" :leftIcon="PlusIcon">
          {{ $t("craftable-pro", "Invite user") }}
        </Button>
      </template>
      <template #title>
        {{ $t("craftable-pro", "Invite user") }}
      </template>
      <template #content>
        <div class="mt-4 flex flex-col gap-2">
          <TextInput
            v-model="form.email"
            name="email"
            :label="$t('craftable-pro', 'Email')"
          />
          <Multiselect
            v-model="form.role_id"
            name="role"
            :label="$t('craftable-pro', 'Role')"
            mode="single"
            :options="filterOptions.roles"
            optionsValueProp="id"
            optionsLabel="name"
          />
        </div>
      </template>
      <template #buttons="{ setIsOpen }">
        <Button size="sm" :loading="form.processing" @click="submit(setIsOpen)">
          {{ $t("craftable-pro", "Invite user") }}
        </Button>
        <Button
          size="sm"
          color="gray"
          variant="outline"
          @click.prevent="() => setIsOpen()"
        >
          {{ $t("craftable-pro", "Cancel") }}
        </Button>
      </template>
    </Modal>
  </PageHeader>

  <PageContent>
    <Listing
      :baseUrl="route('craftable-pro.craftable-pro-users.index')"
      :data="craftableProUsers"
      dataKey="craftableProUsers"
    >
      <template #actions>
        <FiltersDropdown
          :activeFiltersCount="activeFiltersCount"
          :resetFilters="resetFilters"
        >
          <Multiselect
            v-model="filtersForm.role"
            name="role"
            :label="$t('craftable-pro', 'Role')"
            :options="filterOptions.roles"
            optionsValueProp="id"
            optionsLabel="name"
          />
          <Multiselect
            v-model="filtersForm.status"
            name="status"
            :label="$t('craftable-pro', 'Status')"
            :options="statusOptions"
            options-value-prop="value"
            options-label="label"
            mode="single"
          />
          <Multiselect
            v-model="filtersForm.two_factor_auth_enabled"
            name="two_factor_auth_enabled"
            :label="$t('craftable-pro', '2FA')"
            :options="twoFactorAuthOptions"
            options-value-prop="value"
            options-label="label"
            mode="single"
          />
        </FiltersDropdown>
      </template>

      <template #bulkActions="{ baseUrl, bulkAction }">
        <!-- TODO: there was some kind of an idea to soft/force destroy? -->
        <Button
          @click="() => bulkAction('post', `${baseUrl}/bulk-activate`)"
          color="gray"
          variant="outline"
          size="sm"
          :leftIcon="ShieldCheckIcon"
          v-can="'craftable-pro.craftable-pro-user.edit'"
        >
          {{ $t("craftable-pro", "Activate") }}
        </Button>

        <Modal type="danger" v-can="'craftable-pro.craftable-pro-user.destroy'">
          <template #trigger="{ setIsOpen }">
            <Button
              @click="setIsOpen(true)"
              color="gray"
              variant="outline"
              size="sm"
              :leftIcon="NoSymbolIcon"
            >
              {{ $t("craftable-pro", "Deactivate") }}
            </Button>
          </template>

          <template #title
            >{{ $t("craftable-pro", "Deactivate users") }}
          </template>

          <template #content>
            {{
              $t(
                "craftable-pro",
                "Are you sure you want to deactivate selected users?"
              )
            }}
          </template>

          <template #buttons="{ setIsOpen }">
            <Button
              @click.prevent="
                () => bulkAction('post', `${baseUrl}/bulk-deactivate`)
              "
              color="danger"
            >
              {{ $t("craftable-pro", "Deactivate") }}
            </Button>
            <Button
              @click.prevent="() => setIsOpen()"
              color="gray"
              variant="outline"
            >
              {{ $t("craftable-pro", "Cancel") }}
            </Button>
          </template>
        </Modal>

        <Modal type="danger" v-can="'craftable-pro.craftable-pro-user.destroy'">
          <template #trigger="{ setIsOpen }">
            <Button
              @click="() => setIsOpen(true)"
              color="gray"
              variant="outline"
              size="sm"
              :leftIcon="TrashIcon"
            >
              {{ $t("craftable-pro", "Delete") }}
            </Button>
          </template>

          <template #title>{{ $t("craftable-pro", "Delete users") }} </template>
          <template #content>
            {{
              $t(
                "craftable-pro",
                "Are you sure you want to delete selected users? All of their data will be permanently removed from our servers forever. This action cannot be undone."
              )
            }}
          </template>

          <template #buttons="{ setIsOpen }">
            <!-- TODO: disable button while submitting... (done in other PR) -->
            <Button
              @click.prevent="
                () => {
                  bulkAction('delete', `${baseUrl}/bulk-destroy`, {
                    onFinish: () => setIsOpen(false),
                  });
                }
              "
              color="danger"
            >
              {{ $t("craftable-pro", "Delete") }}
            </Button>
            <Button
              @click.prevent="() => setIsOpen()"
              color="gray"
              variant="outline"
            >
              {{ $t("craftable-pro", "Cancel") }}
            </Button>
          </template>
        </Modal>
      </template>

      <template #tableHead>
        <ListingHeaderCell sortBy="id" class="w-14">
          {{ $t("craftable-pro", "ID") }}
        </ListingHeaderCell>

        <ListingHeaderCell sortBy="first_name">
          {{ $t("craftable-pro", "User") }}
        </ListingHeaderCell>

        <ListingHeaderCell>
          {{ $t("craftable-pro", "Role") }}
        </ListingHeaderCell>

        <ListingHeaderCell class="text-center">
          {{ $t("craftable-pro", "2FA") }}
        </ListingHeaderCell>

        <ListingHeaderCell>
          {{ $t("craftable-pro", "Status") }}
        </ListingHeaderCell>

        <ListingHeaderCell
          v-if="$page.props.config?.craftable_pro?.track_user_last_active_time"
          sortBy="last_active_at"
        >
          {{ $t("craftable-pro", "Last active") }}
        </ListingHeaderCell>

        <ListingHeaderCell>
          <span class="sr-only">{{ $t("craftable-pro", "Actions") }}</span>
        </ListingHeaderCell>
      </template>

      <template #tableRow="{ item, action }: any">
        <ListingDataCell>
          {{ item.id }}
        </ListingDataCell>

        <ListingDataCell>
          <div class="flex items-center">
            <Avatar
              :src="item.avatar_url"
              :name="`${item.first_name} ${item.last_name}`"
            />
            <div class="ml-4">
              <div class="font-medium text-gray-900">
                <!-- TODO: maybe have full_name attribute? -->
                {{ item.first_name }} {{ item.last_name }}
              </div>
              <div class="text-gray-500">{{ item.email }}</div>
            </div>
          </div>
        </ListingDataCell>

        <ListingDataCell>
          <span class="text-sm font-normal leading-5 text-slate-500">
            {{ item.roles.length > 0 ? item.roles[0].name : "" }}
          </span>
        </ListingDataCell>

        <ListingDataCell class="text-center">
          <Tooltip v-if="item.has_enabled_two_factor_authentication">
            <template #button>
              <CheckCircleIconOutline
                class="h-6 w-6 text-success-400 stroke-2 mx-auto"
              />
            </template>
            <template #content>
              {{ $t("craftable-pro", "2FA is enabled") }}
            </template>
          </Tooltip>
          <Tooltip v-else-if="item.roles.filter((role) => role.two_factor_auth_required === true).length > 0">
            <template #button>
              <ExclamationCircleIconOutline
                class="h-6 w-6 text-danger-400 stroke-2 mx-auto"
              />
            </template>
            <template #content>
              {{ $t("craftable-pro", "2FA is disabled") }}
            </template>
          </Tooltip>


          <Tooltip v-else>
            <template #button>
              <MinusCircleIconOutline
                class="h-6 w-6 text-gray-400 stroke-2 mx-auto"
              />
            </template>
            <template #content>
              {{ $t("craftable-pro", "2FA is not required") }}
            </template>
          </Tooltip>
        </ListingDataCell>

        <ListingDataCell class="text-left">
          <template v-if="item.email_verified_at">
            <div v-if="item.active">
              <Tag :icon="CheckCircleIcon" color="success" rounded size="sm">
                {{ $t("craftable-pro", "Active") }}
              </Tag>
            </div>

            <div v-else>
              <Tag :icon="XCircleIcon" color="gray" rounded size="sm">
                {{ $t("craftable-pro", "Inactive") }}
              </Tag>
            </div>
          </template>

          <div v-else>
            <Tag
              :icon="ExclamationCircleIcon"
              color="warning"
              rounded
              size="sm"
            >
              {{ $t("craftable-pro", "Pending") }}
            </Tag>
          </div>
        </ListingDataCell>

        <ListingDataCell
          v-if="$page.props.config?.craftable_pro?.track_user_last_active_time"
        >
          <div v-if="item.email_verified_at" class="flex gap-2">
            <div v-if="item.last_active_at === null">
              {{ $t("craftable-pro", "Never") }}
            </div>

            <template v-else>
              <div class="flex flex-col justify-center">
                <ClockIcon class="h-4 w-4" />
              </div>
              <div>
                {{ dayjs(item.last_active_at).format("DD.MM.YYYY HH:mm") }}
              </div>
            </template>
          </div>

          <template v-else>
            <Button
              variant="outline"
              color="gray"
              @click.prevent="
                () => {
                  action(
                    'post',
                    `craftable-pro-users/${item.id}/resend-verification-email`
                  );
                }
              "
              size="sm"
            >
              {{ $t("craftable-pro", "Resend invitation") }}
            </Button>
          </template>
        </ListingDataCell>

        <ListingDataCell>
          <div class="flex justify-end">
            <Dropdown
              noContentPadding
              :placement="isLastThreeItems(item) ? 'bottom-end' : 'top-end'"
            >
              <template #button>
                <IconButton
                  :icon="EllipsisVerticalIcon"
                  variant="outline"
                  color="gray"
                  size="sm"
                />
              </template>

              <template #content class="bg-red">
                <div class="py-1">
                  <DropdownItem
                    v-can="'craftable-pro.craftable-pro-user.edit'"
                    :href="`${item.resource_url}/edit`"
                    :icon="PencilSquareIcon"
                  >
                    {{ $t("craftable-pro", "Edit") }}
                  </DropdownItem>

                  <template v-if="item.email_verified_at">
                    <DropdownItem
                      @click="changeActiveStatus(item)"
                      :icon="item.active ? NoSymbolIcon : ShieldCheckIcon"
                    >
                      {{
                        item.active
                          ? $t("craftable-pro", "Deactivate")
                          : $t("craftable-pro", "Activate")
                      }}
                    </DropdownItem>
                  </template>

                  <DropdownItem
                    v-else
                    @click="
                      () => {
                        action(
                          'post',
                          `craftable-pro-users/${item.id}/resend-verification-email`
                        );
                      }
                    "
                    :icon="EnvelopeIcon"
                  >
                    {{ $t("craftable-pro", "Resend invitation") }}
                  </DropdownItem>

                  <div>
                    <Modal
                      type="danger"
                      v-can="'craftable-pro.craftable-pro-user.destroy'"
                    >
                      <template #trigger="{ setIsOpen }">
                        <div
                          @click="() => setIsOpen(true)"
                          class="flex cursor-pointer gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                        >
                          <div class="flex flex-col justify-center">
                            <TrashIcon class="h-4 w-4" />
                          </div>
                          {{ $t("craftable-pro", "Delete") }}
                        </div>
                      </template>

                      <template #title>
                        {{ $t("craftable-pro", "Delete user") }}
                      </template>

                      <template #content>
                        {{
                          $t(
                            "craftable-pro",
                            "Are you sure you want to delete selected user? All of his data will be permanently removed from our servers forever. This action cannot be undone."
                          )
                        }}
                      </template>

                      <template #buttons="{ setIsOpen }">
                        <Button
                          @click.prevent="
                            () => {
                              action('delete', item.resource_url, {
                                onFinish: () => setIsOpen(false),
                              });
                            }
                          "
                          color="danger"
                        >
                          {{ $t("craftable-pro", "Delete") }}
                        </Button>
                        <Button
                          @click.prevent="() => setIsOpen()"
                          color="gray"
                          variant="outline"
                        >
                          {{ $t("craftable-pro", "Cancel") }}
                        </Button>
                      </template>
                    </Modal>
                  </div>

                  <DropdownItem
                    v-can="'craftable-pro.craftable-pro-user.impersonal-login'"
                    v-if="item.id !== $page.props.auth.user.id"
                    :href="
                      route(
                        'craftable-pro.craftable-pro-user.impersonalLogin',
                        {
                          craftableProUser: item.id,
                        }
                      )
                    "
                    :icon="ArrowLeftOnRectangleIcon"
                  >
                    {{ $t("craftable-pro", "Log as user") }}
                  </DropdownItem>
                </div>
              </template>
            </Dropdown>
          </div>
        </ListingDataCell>
      </template>
    </Listing>
  </PageContent>
</template>

<script setup lang="ts">
import { Link, usePage, useForm } from "@inertiajs/vue3";

import {
  PlusIcon,
  TrashIcon,
  PencilSquareIcon,
  ClockIcon,
  ArrowLeftOnRectangleIcon,
  EllipsisVerticalIcon,
  EnvelopeIcon,
} from "@heroicons/vue/24/outline";
import { NoSymbolIcon, ShieldCheckIcon } from "@heroicons/vue/24/solid";
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  XCircleIcon,
} from "@heroicons/vue/20/solid";
import {
  CheckCircleIcon as CheckCircleIconOutline,
  ExclamationCircleIcon as ExclamationCircleIconOutline,
  MinusCircleIcon as MinusCircleIconOutline,
} from "@heroicons/vue/24/outline";
import {
  PageHeader,
  PageContent,
  Button,
  Listing,
  Avatar,
  ListingHeaderCell,
  ListingDataCell,
  Modal,
  IconButton,
  FiltersDropdown,
  Multiselect,
  Tag,
  Dropdown,
  DropdownItem,
  TextInput,
} from "craftable-pro/Components";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import type { CraftableProUser } from "craftable-pro/types/models";
import { useAction } from "craftable-pro/hooks/useAction";
import { useListingFilters } from "craftable-pro/hooks/useListingFilters";
import { PageProps } from "craftable-pro/types/page";
import { wTrans } from "craftable-pro/plugins/laravel-vue-i18n";
import dayjs from "dayjs";
import { CraftableProUserInviteUserForm } from "./types";
import { useToast } from "@brackets/vue-toastification";
import {Tooltip} from "../../Components";

interface Props {
  craftableProUsers: PaginatedCollection<CraftableProUser>;
  filterOptions: {
    roles: string[];
  };
}

const { action } = useAction();

const changeActiveStatus = (item: CraftableProUser) => {
  action("patch", route("craftable-pro.craftable-pro-users.update", item.id), {
    active: !item.active,
  });
};

const statusOptions = [
  { value: "true", label: wTrans("craftable-pro", "Active") },
  { value: "false", label: wTrans("craftable-pro", "Inactive") },
  { value: "pending", label: wTrans("craftable-pro", "Pending") },
];

const twoFactorAuthOptions = [
  { value: "true", label: wTrans("craftable-pro", "Active") },
  { value: "false", label: wTrans("craftable-pro", "Inactive") },
];

const props = defineProps<Props>();

const { filtersForm, resetFilters, activeFiltersCount } = useListingFilters(
  "/admin/craftable-pro-users",
  {
    role: (usePage().props as PageProps).filter?.role ?? null,
    status: (usePage().props as PageProps).filter?.status ?? null,
    two_factor_auth_enabled: (usePage().props as PageProps).filter?.two_factor_auth_enabled ?? null,
  }
);

const isLastThreeItems = (item: CraftableProUser) => {
  const arrLength = props.craftableProUsers.data.length;

  let lastElement = props.craftableProUsers.data[arrLength - 1];
  let beforeLastElement = props.craftableProUsers.data[arrLength - 2];

  return lastElement.id === item.id || beforeLastElement.id === item.id;
};

const toast = useToast();

const form = useForm({
  email: "",
  role_id: "",
});

const submit = (closeModal: CallableFunction) => {
  form.post(route("craftable-pro.craftable-pro-user.invite-user"), {
    onSuccess: () => {
      form.email = "";
      form.role_id = "";

      closeModal();

      toast.success(usePage().props?.message);
    },
    onError: (errors: Errors) => {
      if (errors && Object.values(errors)) {
        toast.error(Object.values(errors)[0]);
      }
    },
  });
};
</script>
