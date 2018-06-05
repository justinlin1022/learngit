<template>
  <el-row :gutter="20">
    <!-- 数据库信息 begin -->
    <el-row class="title">数据库信息</el-row>
    <el-row class="form-wrapper">
      <el-form :model="form" ref="form" :rules="rules" label-width="130px">
        <el-col :span="8">
          <el-form-item label="数据库主机ip">
            <el-input v-model="form.db.host"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="数据库用户名">
            <el-input v-model="form.db.username"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="数据库密码">
            <el-input v-model="form.db.password"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="数据库名">
            <el-input v-model="form.db.database"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="数据库表名">
            <el-input v-model="form.db.tableName"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item>
            <el-button type="primary" @click="handleConnect">点击连接</el-button>
          </el-form-item>
        </el-col>
      </el-form>
    </el-row>
    <!-- 数据库信息 end -->

    <!-- 导航信息 begin -->
    <el-row class="title">导航信息</el-row>
    <el-row class="form-wrapper">
      <el-form :model="form" ref="form" :rules="rules" label-width="130px">
        <el-col :span="8">
          <el-form-item label="页面导航名称">
            <el-input v-model="form.nav_name"></el-input>
          </el-form-item>
        </el-col>
        <!-- <el-col :span="4">
          <el-form-item label="使用已有导航分类">
            <el-switch v-model="form.isNavExit"></el-switch>
          </el-form-item>
        </el-col>
        <el-col :span="8" v-if="form.isNavExit != undefined && !form.isNavExit">
          <el-form-item label="新建分类">
            <el-input v-model="form.nav_name"></el-input>
          </el-form-item>
        </el-col> -->
        <el-col :span="8"><!-- v-if="form.isNavExit" -->
          <el-form-item label="页面所属分类">
            <el-select v-model="form.nav_cat_id" placeholder="请选择页面所属分类">
              <el-option 
                v-for="item in navCatList" 
                :label="item.cat_name" 
                :value="item.id"
                :key="index">
              </el-option>
            </el-select>
          </el-form-item>
        </el-col>
      </el-form>
    </el-row>
    <!-- 导航信息 end -->

    <!-- 字段信息 begin -->
    <template v-if="form.fields.length">
      <el-row class="title">字段信息</el-row>
      <template v-for="field in form.fields" v-if="form.fields.length && !field.isDefaultField" v-key="index">
        <field :field="field"></field>
      </template>
      <el-col :span="8" :offset="20">
        <el-button type="primary" @click="handleCreateFields">生成页面字段</el-button>
      </el-col>
    </template>
    <!-- 字段信息 end -->

    <!-- <el-row class="title">页面权限</el-row> -->
  </el-row>
</template>

<script>
  import ajax from 'admin/api/ajax';
  import defaultFields from 'admin/assets/js/defaultFields';
  import field from './FieldConfig';
  export default {
    props: {
      id: {
        type: [String, Number]
      }
    },
    data() {
      return {
        allFieldsUrl: `${window.base_url}/config/allFields`,
        modifyFieldsUrl: `${window.base_url}/config/modifyFields`,
        getConfigInfoUrl: `${window.base_url}/config/configInfo`,
        catListUrl: `${window.base_url}/nav/catList`,
        form: {
          db: {
            host: 'localhost',
            username: 'root',
            password: 'kf_test@24.96',
            database: 'cfg',
            tableName: 't_test',
          },
          fields: [],
          nav_name: '',
          nav_cat_id: ''
        },
        // fields字段属性：type, checkType, isRequired, isListField, isSearchField, placeholder, category
        navCatList: [],
        rules: {
        }
      }
    },
    mounted() {
      if (this.id) {
        this.getConfigInfo();
      }
      // this.getCatList();
    },
    methods: {
      getConfigInfo() {
        // console.log('this.id: ', this.id);
        ajax({ url: this.getConfigInfoUrl, data: { id: this.id } }).then((res) => {
          if (res.status === 200 && res.data && res.data.retcode === 0) {
            let data = res.data.data[0];
            try {
              this.form = data;
              this.form.fields = JSON.parse(data.fields);
            } catch (err) {
              console.log('getConfigInfo error: ', err);
            }
          }
        }).catch((err) => {
          console.log('err: '. err);
        })
      },
      getCatList() {
        ajax({
          method: 'get',
          url: this.catListUrl
          }).then(res => {
          if (res.status === 200 && res.data && res.data.retcode === 0) {
            let dataArr = res.data.data;
            this.navCatList = dataArr;
            console.log('navCatList: ', this.navCatList);
            
          }
        }).catch(err => {
          console.log('err: '. err);
        })
      },
      handleConnect() {
        // 校验，接口发送数据，获取响应数据，展示
        ajax({
          method: 'post',
          url: this.allFieldsUrl, 
          data: this.form})
          .then((res) => {
          if (res.status === 200 && res.data && res.data.retcode !== -1) {
            // 账号和密码校验成功，跳转后台首页
            this.$message.success('连接成功');
            let data = res.data.data;
            if (data.length) {
              this.form.fields = [];
              data.forEach(fieldName => {
                let config = {};
                config.name = fieldName;
                config.isSearchField = {};
                // if (defaultFields.indexOf(fieldName) < 0) {
                //   config.isDefaultField = false;
                // } else {
                //   config.isDefaultField = true;
                // }
                this.form.fields.push(config);
              });
              console.log('this.form: ', this.form);  
            }
          } else {
            this.$message.warning('连接失败，请重新连接');
          }
        }).catch((err) => {
          console.log('allFieldsUrl fail', err);
          this.$message.error('连接失败，请稍后再试');
        })
      },
      handleCreateFields() {
        let data = {};
        Object.keys(this.form).forEach(key => {
          data[key] = this.form[key];
        })
        // 如果存在id，则为修改页面配置
        if (this.id) {
          data.id = this.id;
        }
        data.fields = this.form.fields;
        console.log('data: ', data);
        
        ajax({
          method: 'post',
          url: this.modifyFieldsUrl, 
          data: data}).then(res => {
          if (res.status === 200 && res.data && res.data.retcode === 0) {
            this.$message.success('生成页面字段成功');
            this.$router.replace('/admin/configlist');
          } else {
            this.$message.warning('生成页面字段失败，请重新生成');
          }
        }).catch(err => {
          console.log('modifyFieldsUrl error: ', err);
          this.$message.error('连接失败，请稍后再试');
        })
      }
    },
    components: {
      field
    }
  }
</script>

<style lang="scss" scoped>
  .title{
    height: 40px;
    line-height: 40px;
    background-color: #e5e5e5;
    margin: 0 10px 20px;
    padding: 0 10px;
    font-size: 16px;
    border-radius: 4px;
  }
  .form-wrapper{
    padding: 0 10px;
  }
  .line{
    position: relative;
    padding: 0;
    margin: 0 10px 20px;
    border-bottom: 1px solid #e5e5e5;
    &:last-child{
      border-bottom: none;
    }
  }
</style>


