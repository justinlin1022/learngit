<template>
  <aside class="main-sidebar">
    <el-menu
      :default-active="$route.path"
      class="el-menu-vertical-demo"
      @open="handleOpen"
      @close="handleClose"
      background-color="#545c64"
      text-color="#fff"
      active-text-color="#409eff"
      unique-opened
      router>
      <!-- el-submenu为一个单位的导航，el-menu-item为具体要跳转的导航 -->
      <el-submenu index="1">
        <template slot="title">
          <i class="el-icon-location"></i>
          <span>配置管理</span>
        </template>
        <el-menu-item index="configlist">
          <i class="el-icon-menu"></i>
          <span slot="title">配置页面</span>
        </el-menu-item>
      </el-submenu>

      <el-submenu index="2">
        <template slot="title">
          <i class="el-icon-location"></i>
          <span>页面管理</span>
        </template>
        <el-menu-item index="list">
          <i class="el-icon-menu"></i>
          <span slot="title">基础模板</span>
        </el-menu-item>
      </el-submenu>

      <el-submenu index="3">
        <template slot="title">
          <i class="el-icon-location"></i>
          <span>管理员管理</span>
        </template>
        <el-menu-item index="manageList">
          <i class="el-icon-menu"></i>
          <span slot="title">管理员列表</span>
        </el-menu-item>
        <el-menu-item index="rightList">
          <i class="el-icon-menu"></i>
          <span slot="title">权限配置</span>
        </el-menu-item>
      </el-submenu>

    </el-menu>
  </aside>
</template>

<script>
  export default {
    // props: {
    //   navData: {
    //     type: Array,
    //     default: []
    //   }
    // },
    mounted() {
      // console.log('this.$route: ', this.$route);
    },
    methods: {
      handleOpen() {},
      handleClose() {}
    }
  }
</script>

<style lang="scss" scoped>
  .main-sidebar{
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    padding-top: 60px;
    overflow-y: auto;
    width: 230px;
    background-color: rgb(84, 92, 100);
  }
  .el-menu{
    border: none;
  }
</style>


