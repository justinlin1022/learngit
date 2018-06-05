<template>
  <el-row>
    <!-- 查询选项 begin -->
    <el-row :gutter="20" class="mb20">
      <el-col :span="6">
        <el-input placeholder="请输入卷宗ID" v-model="search.jz_id.value">
          <template slot="prepend">卷宗ID</template>
        </el-input>
      </el-col>
      <el-col :span="6">
        <el-input placeholder="请输入账号ID" v-model="search.username.value">
          <template slot="prepend">账号ID</template>
        </el-input>
      </el-col>
      <el-col :span="6">
        <el-button type="primary" @click="handleSearch">查询</el-button>
        <el-button type="primary" @click="handleAddPage">新建页面</el-button>
      </el-col>
    </el-row>
    <!-- 查询选项 end -->

    <!-- 列表展示 begin -->
    <el-row :gutter="20">
      <el-col :span="24">
        <el-table
          v-loading="loading"
          :data="tableData"
          border
          style="width:100%">
          <el-table-column prop="id" label="ID" width="150"></el-table-column>
          <el-table-column prop="host" label="host" width="120"></el-table-column>
          <el-table-column prop="user" label="用户名" width="120"></el-table-column>
          <el-table-column prop="password" label="密码" width="120"></el-table-column>
          <el-table-column prop="databaseName" label="数据库名" width="120"></el-table-column>
          <el-table-column prop="tableName" label="表名" width="120"></el-table-column>
          <el-table-column prop="nav_name" label="导航名"></el-table-column>
          <el-table-column fixed="right" label="操作" width="200">
            <template slot-scope="scope">
              <el-button @click="handleEdit(scope.$index, scope.row)">修改</el-button>
              <el-button @click="handleDelete(scope.$index, scope.row)">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-col>
    </el-row>
    <!-- 列表展示 end -->

    <!-- 分页 begin -->
    <el-row :gutter="20">
      <el-col :offset="18">
        <el-pagination
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
          :current-page="pagination.current_page"
          :page-sizes="[10, 20, 40]"
          :page-size="pagination.per_page"
          layout="total, sizes, prev, pager, next"
          :total="pagination.total">
        </el-pagination>
      </el-col>
    </el-row>
    <!-- 分页 end -->

    <!-- 详情弹窗组件 begin -->
    <!-- 详情弹窗组件 end -->
  </el-row>
</template>

<script>
  import ajax from 'admin/api/ajax';
  export default {
    data() {
      return {
        listUrl: `${window.base_url}/config/configList`,
        search: {
          jz_id: {value: '', operator: '='},
          username: {value: '', operator: '='}
        },
        tableData: [],
        pagination: {
          total: 0,
          per_page: 10,
          from: 1,
          to: 0,
          current_page: 1
        },
        loading: true
      }
    },
    mounted() {
      this.fetchItems();
    },
    methods: {
      handleEdit(index, row) {
        let id = row.id;
        this.$router.push(`configmodify/${id}`);
      },
      handleDelete(index, row) {
        // console.log(index, row);
      },
      handleSizeChange(val) {
        this.pagination.per_page = val;
        this.fetchItems(this.pagination.current_page);
      },
      handleCurrentChange(val) {
        this.fetchItems(val);
      },
      handleSearch() {
        
      },
      handleAddPage() {
        this.$router.push('/admin/configmodify');
      },
      fetchItems(page) {
        let data = {};

        ajax({ url: this.listUrl, data: data}).then((res) => {
          console.log('res: ', res);
          this.loading = false;
          if (res.status === 200 && res.data && res.data.retcode === 0) {
            let data = res.data.data.data;
            this.tableData = data;
            console.log(this.tableData);
          }
        }).catch((err) => {
          console.log('err: ', err);
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  .mb20{
    margin-bottom: 20px;
  }
</style>
