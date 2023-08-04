<template>
  <v-menu open-delay="50" location="top" class="rounded-lg" :open-on-hover="!isMobile()"
          :open-on-click="isMobile()" :close-on-content-click="false" :transition="false">
    <template v-slot:activator="{ props }">
      <v-btn value="access" v-bind="props" variant="text" rounded="0" class="text-body-2">Access</v-btn>
    </template>
    <v-list class="rounded-lg" density="compact">
      <v-list-item v-if="showOwnership()" density="compact" @click="takeOwnerShip()">Take ownership</v-list-item>
      <v-divider v-if="showOwnership()"></v-divider>
      <div v-if="this.ownership && this.isOwner">
        <v-list-item density="compact" value="setPinCode" v-if="!hasPinCode" @click="openPinCode()">Set pin code</v-list-item>
        <v-list-item density="compact" class="text-green" v-if="hasPinCode">Pin Code</v-list-item>
        <v-list-item density="compact" value="changePinCode" v-if="hasPinCode"  @click="openPinCode()">Change</v-list-item>
        <v-list-item density="compact" value="deletePinCode" v-if="hasPinCode" @click="removePinCode()">Turn off</v-list-item>
        <v-divider></v-divider>
      </div>
      <v-list-item density="compact" class="text-green">Sharing link</v-list-item>
      <v-list-item density="compact" value="fullAccess" @click="copyFullAccessLink()">Full access</v-list-item>
      <v-list-item density="compact" value="readOnly" @click="copyReadOnlyLink()">Read-only</v-list-item>
    </v-list>
  </v-menu>
  <div ref="copyLink" class="hidden"></div>
  <div class="text-center">
    <v-dialog
        v-model="openPinCodeDialog"
        width="auto"
    >
      <v-card class="pa-5" min-width="250" max-width="300">
        <div>A user with a pin code will get full access to the board and will be able to disable or change the pin code</div>
        <v-form @submit.prevent="submit" validate-on="submit lazy" ref="form">
          <v-text-field :rules="rules" v-model="pinCode" type="string" placeholder="" variant="underlined"></v-text-field>
          <v-btn :loading="loading" type="submit" variant="tonal" width="100%" class="mt-2 text-body-1 font-weight-medium">Set access to pin code</v-btn>
        </v-form>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>

import {mobile, user} from '../../mixins';

export default {
  name: 'TheAccess',
  props: ['readOnly', 'readOnlyUrl', 'ownership', 'hasPinCode', 'isOwner'],
  data() {
    return {
      openPinCodeDialog: false,
      loading: false,
      pinCode: null,
      rules: [
        v => !v || v.length === 6 || 'Pin code length is 6 symbols'
      ]
    };
  },
  mixins: [mobile, user],
  emits: ['takeOwnership', 'pinCode:setUp', 'pinCode:remove'],
  methods: {
    showOwnership() {
      return this.user.authorized && !this.ownership;
    },
    copyFullAccessLink() {
      this.copyLink(window.location.href)
    },
    copyReadOnlyLink() {
      this.copyLink(this.readOnlyUrl)
    },
    copyLink(link) {
      const el = document.createElement('textarea');
      el.value = link;
      el.setAttribute('readonly', '');
      el.style.position = 'absolute';
      el.style.left = '-9999px';
      document.body.appendChild(el);
      const selected = document.getSelection().rangeCount > 0 ? document.getSelection().getRangeAt(0) : false;
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);
      if (selected) {
        document.getSelection().removeAllRanges();
        document.getSelection().addRange(selected);
      }
    },
    takeOwnerShip() {
      this.$emit('takeOwnership')
    },
    openPinCode() {
      this.openPinCodeDialog = true;
    },
    removePinCode() {
      this.$emit('pinCode:remove')
    },
    async submit () {
      this.loading = true

      const { valid } = await this.$refs.form.validate()

      if (valid) {
        this.$emit('pinCode:setUp', this.pinCode)
        this.$refs.form.reset()
        this.loading = false
        this.openPinCodeDialog = false
      } else {
        this.loading = false
      }
    }
  }
};
</script>