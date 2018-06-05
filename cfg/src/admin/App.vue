<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script type="text/ecmascript-6">
  import 'admin/assets/js/test';
  export default {
    data() {
      return {
        text: 'App'
      }
    },
    // render(createElement) {
    //   return createElement('input', {}, []);
    // },
    // 列举所有事件
    beforeCreate() {
      console.log('test');
      console.log('testests');
      
      console.log('test');
    },
    created() {

    },
    beforeMount() {

    },
    mounted() {
    },
    beforeUpdate() {

    },
    updated() {
    },
    beforeDestroy() {
    },
    destroyed() {

    }
  }
</script>
  
<style scoped>
  @import '~admin/assets/styles/test-sass';
  
</style>
