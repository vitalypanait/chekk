<template>
  <v-container class="my-auto">
    <v-row align="center" justify="center" class="my-auto">
      <v-col class="v-col-4">
        <v-form @submit.prevent="submit" validate-on="submit lazy" ref="form">
          <v-text-field :rules="rules" v-model="email" type="email" placeholder="Email" variant="underlined"></v-text-field>
          <v-btn :loading="loading" type="submit" variant="tonal" width="100%" class="mt-2 text-body-1 font-weight-medium" >Get login link</v-btn>
        </v-form>
        <div v-if="link !== null" class="mt-4">
          <a :href="link">Link to login</a>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>

import axios from 'axios';

export default {
  name: 'Auth',
  data() {
    return {
      loading: false,
      email: null,
      link: null,
      rules: [
        v => !v || /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || 'E-mail must be valid'
      ]
    };
  },
  methods: {
    async submit () {
      this.loading = true

      const { valid } = await this.$refs.form.validate()

      if (valid) {
        axios
            .post('/auth', {email: this.email})
            .then(response => {
              this.link = response.data.link

              this.loading = false
            });
      } else {
        this.loading = false
      }
    }
  }
};
</script>
