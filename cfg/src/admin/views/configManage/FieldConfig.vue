<template>
  <el-row class="form-wrapper line">
    <el-form :model="field" :ref="field.name" label-width="130px">
      <el-col :span="8">
        <el-form-item :label="field.name">
          <el-input v-model="field.label" placeholder="请输入字段中文名"></el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="字段类型">
          <el-select v-model="field.type" placeholder="请选择字段类型" @change="handleTypeChange">
            <el-option label="输入框" value="text"></el-option>
            <el-option label="下拉单选框" value="select"></el-option>
            <el-option label="单选按钮" value="switch"></el-option>
            <el-option label="日期选择器" value="datePicker"></el-option>
            <el-option label="长文本" value="textarea"></el-option>
            <el-option label="富文本" value="editor"></el-option>
            <el-option label="上传" value="upload"></el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="校验类型">
          <el-select v-model="field.checkType" placeholder="请选择校验类型">
            <el-option label="无" value="none"></el-option>
            <el-option label="手机号" value="mobile"></el-option>
            <el-option label="身份证号" value="idcard"></el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <!-- 字段类型为下拉选项时显示 begin -->
      <template v-if="field.type === 'select'">
        <el-col :span="24">
          <el-col :span="16">
            <el-form-item
              v-for="(option, index) in field.options"
              :label="'选项' + index"
              :key="index">
              <el-input v-model="option.label" placeholder="标签label" class="option"></el-input>
              <el-input v-model="option.value" placeholder="值value" class="option"></el-input>
              <el-button @click.prevent="removeOption(option)" :disabled="field.options.length <=1">删除</el-button>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item>
              <el-button @click="addOption">新增域名</el-button>
            </el-form-item>
          </el-col>
        </el-col>
      </template>
      <!-- 字段类型为下拉选项时显示 end -->
      <el-col :span="8">
        <el-form-item label="是否必填">
          <el-switch v-model="field.isRequired"></el-switch>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="是否为搜索字段">
          <el-switch v-model="field.isSearchField.value" @change="handleSearchChange"></el-switch>
          <!-- 搜索字段为true时显示 begin -->
          <template v-if="field.isSearchField.value">
            <el-radio-group v-model="field.isSearchField.operator">
              <el-radio label="="></el-radio>
              <el-radio label="likeboth"></el-radio>
            </el-radio-group>
          </template>
          <!-- 搜索字段为true时显示 end -->
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="是否为展示字段">
          <el-switch v-model="field.isListField"></el-switch>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="提示文字">
          <el-input v-model="field.placeholder"></el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="所属分类">
          <el-input v-model="field.category"></el-input>
        </el-form-item>
      </el-col>
    </el-form>
  </el-row>
</template>

<script>
  export default {
    props: {
      field: {
        type: Object,
        default: {}
      }
    },
    methods: {
      removeOption(item) {
        if (this.field.options.length <= 1) return;
        var index = this.field.options.indexOf(item)
        if (index !== -1) {
          this.field.options.splice(index, 1)
        }
        this.$forceUpdate();
      },
      addOption() {
        this.field.options.push({
          label: '',
          value: ''
        });
        this.$forceUpdate();
      },
      handleTypeChange(type) {
        // 没有选择或者不是select字段类型就不加options
        if (type !== 'select') {
          delete this.field.options;
        } else {
          this.field.options = [];
          this.field.options.push({
            label: '',
            value: ''
          })
        }
      },
      handleSearchChange(newVal) {
        console.log('newval: ', newVal);
        
        // if (newVal) {
        //   this.field.isSearchField = {};
        // } else {
        //   delete this.field.isSearchField;
        // }
      }
    }
  }
</script>

<style lang="scss" scoped>
  .option{
    width: auto;
  }
  .el-radio+.el-radio{
    margin-left: 15px;
  }
  .el-radio-group{
    margin-left: 10px;
  }
</style>
