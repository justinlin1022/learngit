<!-- 后台页面结构入口 -->
<template>
    <el-row>
      <!-- main content -->
      <main class="main-content">
        <!-- <transition name="bounce"> -->
          <router-view></router-view>
        <!-- </transition> -->
      </main>
    </el-row>
</template>

<script>
  import Header from 'admin/components/header/header';
  import Menu from 'admin/components/menu/menu';
  export default {
    data() {
      const item = {
        date: '2016-05-02',
        name: '王小虎',
        address: '上海市普陀区金沙江路 1518 弄'
      };
      return {
        tableData: Array(20).fill(item)
      }
    },
    components: {
      'b-menu': Menu,
      'b-header': Header
    }
  }
</script>

<style  lang="scss" scoped>
  .main-content{
    // margin-left: 230px;
    // margin-top: 60px;
    padding: 10px;
  }

  .bounce-enter-active {
    animation: bounce-in .5s;
  }
  .bounce-leave-active {
    animation: bounce-out .2s;
  }
  @keyframes bounce-in {
    0%{transform: scale(0);}
    50%{transform: scale(1.05);}
    100%{transform: scale(1);}
  }
  @keyframes bounce-out {
    0% {transform: scale(1)}
    50% {transform: scale(0.95)}
    100% {transform: scale(0)}
  }
</style>


