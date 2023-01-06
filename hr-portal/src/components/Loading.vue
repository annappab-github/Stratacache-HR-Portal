<template>
  <div class="loading-animation">
    <div class="loading-text">{{ t('loadingflights.retrieving', {}, {locale: lang})}}<br>{{ t('loadingflights.flights', {}, {locale: lang})}}</div>
    <div class="loading-icon-container">
      <span class="loading-icon mdi mdi-airplane"></span>
    </div>
  </div>
</template>
 
<script>
import {useI18n} from 'vue-i18n';

export default {
  name: 'Loading',
  setup() {
    const {t, locale } = useI18n();
    return {t, locale,       fallbackWarn: false
    };
  },
  data() {
    return {
    };
  },
  mounted() {
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
  .loading-animation {
    width: 100px;
    height: 100px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;

    .loading-text {
      width: 100%;
      height: auto;
      position: relative;
      font-size: 14px;
      text-align: center;
    }

    .loading-icon-container {
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      position: absolute;
      font-size: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: ROTATE-PLANE 5s linear infinite;

      @keyframes ROTATE-PLANE {
        0%   { transform: rotate(360deg); }
        100% { transform: rotate(0deg); }
      }

      .loading-icon {
        width: 30px;
        height: 30px;
        top: calc(50% - 15px);
        left: calc(100% - 15px);
        position: absolute;
        color: $brand-primary;
      }
      
    }
  }
</style>
