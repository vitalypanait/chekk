<template>
  <v-menu open-delay="50" location="top" class="rounded-lg" :open-on-hover="!isMobile()"
          :open-on-click="isMobile()" :close-on-content-click="false" :transition="false">
    <template v-slot:activator="{ props }">
      <v-btn value="access" v-bind="props" variant="text" rounded="0" class="text-body-2">Access</v-btn>
    </template>
    <v-list class="rounded-lg" density="compact">
      <v-list-item density="compact" class="text-green">Sharing link</v-list-item>
      <v-list-item density="compact" value="fullAccess" @click="copyFullAccessLink()">Full access</v-list-item>
      <v-list-item density="compact" value="readOnly" @click="copyReadOnlyLink()">Read-only</v-list-item>
    </v-list>
  </v-menu>
  <div ref="copyLink" class="hidden"></div>
</template>

<script>

import {mobile} from '../../mixins';

export default {
  name: 'TheAccess',
  props: ['readOnly', 'readOnlyUrl'],
  mixins: [mobile],
  methods: {
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
  }
};
</script>