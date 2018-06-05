<template>
  <el-form :model="form" ref="form" :rules="rules" label-width="80px">
    <el-form-item label="账号">
      <el-input v-model="form.name"></el-input>
    </el-form-item>
    <el-form-item label="密码">
      <el-input v-model="form.password"></el-input>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="handleLogin">登录</el-button>
      <el-button>申请</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
  import ajax from 'admin/api/ajax';
    export default {
      data() {
        return {
          loginUrl: `${window.base_url}/login`,
          form: {
            name: '',
            password: ''
          },
          rules: {
          }
        }
      },
      methods: {
        handleLogin() {
          let data = {
            name: this.form.name,
            password: this.form.password
          }
          // 校验，接口发送数据，获取响应数据，展示
          ajax.post(this.loginUrl, data).then((res) => {
            if (res.status === 200 && res.data && res.data.retcode !== -1) {
              // 账号和密码校验成功，跳转后台首页
              this.$message.success('登录成功');
              this.$router.push('/admin');
            } else {
              this.$message.warning('登录失败，请重新登录');
            }
          }).catch((err) => {
            console.log('fail', err);
            this.$message.error('登录失败，请稍后再试');
          })
        }
      }
    }
</script>

<style lang="sass" scoped>

</style>


