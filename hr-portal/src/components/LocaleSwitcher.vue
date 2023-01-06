<template>
  <div>
  <select name="" id="" v-if="this.getLocale == null" @change="change($event.target.value)">
      <option value="en">English</option>
      <option value="du">Dutch</option>
    </select>
    <select name="" id="" v-if="this.getLocale != null" :value="this.getLocale" @change="change($event.target.value)">
      <option value="en">English</option>
      <option value="du">Dutch</option>
    </select>
  </div>
</template>
<script>
export default {
  data() {
    return {
      lang: 'en',
      getLocale: '',
    };
  },
  methods: {
    change(lang) {
      this.lang = lang;
      localStorage.setItem('language', this.lang);
      location.reload();
    },
  },
  mounted() {
    this.getLocale = localStorage.getItem('language');
  },
};
</script>
