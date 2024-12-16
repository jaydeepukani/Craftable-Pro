<template>
  <PageHeader :title="$t('craftable-pro', 'Roles')">
    <Button
      @click.prevent="openRoleModal = true"
      class="mr-2"
      v-can="'craftable-pro.role.store'"
    >
      {{ $t('craftable-pro', "Add role")}}
    </Button>
    <Button :as="Link" :href="`permissions`" v-can="'craftable-pro.role.edit'">
      {{ $t("craftable-pro", "Manage permissions") }}
    </Button>
  </PageHeader>

  <PageContent>
    <Listing
      :baseUrl="route('craftable-pro.roles.index')"
      :data="roles"
      dataKey="roles"
      :withBulkSelect="false"
    >
      <template #tableHead>
        <ListingHeaderCell sortBy="id" class="w-14">
          {{ $t("craftable-pro", "ID") }}
        </ListingHeaderCell>
        <ListingHeaderCell sortBy="name">
          {{ $t("craftable-pro", "Name") }}
        </ListingHeaderCell>
          <ListingHeaderCell>
              {{ $t("craftable-pro", "Enforce 2FA") }}
          </ListingHeaderCell>
        <ListingHeaderCell>
          {{ $t("craftable-pro", "Users") }}
        </ListingHeaderCell>
        <ListingHeaderCell />
      </template>
      <template #tableRow="{ item, action }: any">
        <ListingDataCell>
          {{ item.id }}
        </ListingDataCell>
        <ListingDataCell>
          <div class="font-medium text-gray-900">
            {{ item.name }}
          </div>
        </ListingDataCell>
        <ListingDataCell class="text-left">
            <div v-if="item.two_factor_auth_required">
              <CheckCircleIcon
                class="h-6 w-6 text-success-400 stroke-2"
              />
            </div>

            <div v-else>
              <XCircleIcon
                class="h-6 w-6 text-danger-400 stroke-2"
              />
            </div>
        </ListingDataCell>
        <ListingDataCell>
          <AvatarGroup
            :additionalCount="
              item.users.length > avatarGroupLimit
                ? item.users.length - avatarGroupLimit
                : undefined
            "
            :additionalHref="
              route('craftable-pro.craftable-pro-users.index', {
                filter: { role: [item.name] },
              })
            "
          >
            <Avatar
              v-for="user in item.users.slice(0, avatarGroupLimit)"
              :key="user.id"
              :src="user.avatar_url"
              :name="`${user.first_name} ${user.last_name}`"
            />
          </AvatarGroup>
        </ListingDataCell>
        <ListingDataCell>
          <div class="flex items-center justify-end gap-3">
            <IconButton
              :icon="PencilSquareIcon"
              color="gray"
              variant="ghost"
              @click="editRole(item)"
            />
            <Modal
              type="danger"
              v-can="'craftable-pro.role.destroy'"
            >
              <template #trigger="{ setIsOpen }">
                <IconButton
                  @click="() => setIsOpen(true)"
                  color="gray"
                  variant="ghost"
                  :icon="TrashIcon"
                  v-can="'craftable-pro.role.destroy'"
                />
              </template>

              <template #title>
                {{ $t("craftable-pro", "Delete role") }}
              </template>

              <template #content>
                {{
                  $t(
                    "craftable-pro",
                    "Are you sure you want to delete selected role? All of his data will be permanently removed from our servers forever. This action cannot be undone."
                  )
                }}
              </template>

              <template #buttons="{ setIsOpen }">
                <Button
                  @click.prevent="
                              () => {
                                action('delete', route('craftable-pro.roles.destroy', item), {
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
        </ListingDataCell>
      </template>
    </Listing>
    <Modal :open="openRoleModal" :external-open="true">
      <template #title>
        {{ roleItem?.id ? $t("craftable-pro", "Edit role") : $t("craftable-pro", "Add role") }}
      </template>
      <template #content>
        <TextInput
          name="name"
          v-model="form.name"
          :placeholder='$t("craftable-pro", "Role name")'
        />
          <div class="flex items-center my-4">
              <Toggle
                  v-model="form.two_factor_auth_required"
                  name="two_factor_auth_required"
              />
              <label class="px-2">{{ $t("craftable-pro", "Enforce 2FA") }}</label>
          </div>
      </template>

      <template #buttons>
        <Button @click="submit">
          Save
        </Button>
        <Button @click="openRoleModal=false">
          Cancel
        </Button>
      </template>
    </Modal>
  </PageContent>
</template>

<script setup lang="ts">
import {Link, useForm, usePage, router} from "@inertiajs/vue3";
import {
    PageHeader,
    PageContent,
    Listing,
    ListingHeaderCell,
    ListingDataCell,
    IconButton,
    Button,
    Avatar,
    AvatarGroup,
    Modal,
    TextInput,
    Toggle, Tag
} from "craftable-pro/Components";
import {
  CheckCircleIcon,
  XCircleIcon,
  PencilSquareIcon,
  TrashIcon,
} from "@heroicons/vue/24/outline";
import { PaginatedCollection } from "craftable-pro/types/pagination";
import type { Role } from "craftable-pro/types/models";
import {ref} from "vue";
import { useToast } from "@brackets/vue-toastification";
import {useAction} from "craftable-pro/hooks/useAction";
import {SwitchLabel} from "@headlessui/vue";
import {wTrans} from "craftable-pro/plugins/laravel-vue-i18n";


interface Props {
  roles: PaginatedCollection<Role>;
}

defineProps<Props>();

const avatarGroupLimit = ref(7);

const openRoleModal = ref(false);
const roleItem = ref<Role>();

const toast = useToast();
const {action} = useAction();

const form = useForm({
  name: "",
  two_factor_auth_required: false
});

const editRole = (item: Role) => {
  roleItem.value = item;
  form.name = item.name;
  form.two_factor_auth_required = item.two_factor_auth_required;
  openRoleModal.value = true;
}

const statusOptions = [
    { id: "true", label: wTrans("craftable-pro", "Active") },
    { id: "false", label: wTrans("craftable-pro", "Inactive") },
];

const submit = () => {
  const method = roleItem.value?.id ? 'put' : 'post';
  const url = roleItem.value?.id ? route('craftable-pro.roles.update', roleItem.value?.id) : route('craftable-pro.roles.store');
  router.visit(url, {
    method: method,
    data: form,
    onSuccess: () => {
      form.name = "";
      form.two_factor_auth_required = false;

      openRoleModal.value = false;

      toast.success(usePage().props?.message);
    },
    onError: (errors: Errors) => {
      if (errors && Object.values(errors)) {
        openRoleModal.value = false;
        toast.error(Object.values(errors)[0]);
      }
    },
  })
};

</script>
