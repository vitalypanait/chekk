<template>
  <v-app class="main-background" full-height>
    <v-main>
      <router-view/>
    </v-main>
  </v-app>
</template>

<script>

export default {
  name: 'App'
};
</script>
<style>
  .main-background {
    background: #f0f0f0!important;
  }
</style>