<template>
  <header class="header">
    <a href="" class="logo">内容管理平台</a>
    <nav class="navbar">
      <div class="navbar-custom-menu">
        <span class="txt">您好，{{user.name}}</span>
        <a href="javascript:;" @click="logout">退出</a>
      </div>
    </nav>
  </header>
</template>

<script>
  
  export default {
    data() {
      return {
        user: {
          name: ''
        }
      }
    },
    created() {
      this.getUserName();
    },
    methods: {
      getUserName() {
        if (window._user && window._user.name) {
          this.user = window._user;
        }
      },
      logout() {
        // 请求api清空session，并跳转到登录页面

        this.$router.replace('/login');
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~admin/assets/styles/variable';
  .header{
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    line-height: 60px;
    overflow: hidden;
    background-color: #409eff;
    color: #fff;
    padding: 0 20px;
    z-index: 1;
    .logo{
      float: left;
      color: #fff;
    }
    .navbar{
      float: right;
      a{
        color: #fff;
      }
    }
  }
  .el-header{
    background-color: #409eff;
    color: #fff;
    width: 100%;
  }
</style>


