<!-- 展示模板 -->
<template>
    <!-- <ErrorTip v-if="notAccess" msg="您无权限访问哟~"></ErrorTip> -->
    <el-row>
        <!-- 查询选项 -->
        <el-row :gutter="24" class="pb15">
            <div v-if="searchelemt.length > 0" v-for="(item,index) in searchelemt" :key="index">
                <!-- 单选框类型 -->
                <!-- <el-col v-if="item.type == 'radio' && item.isAutocomplete == true" :span="6" class="pb15" >
                    <div class="el-input el-input-group el-input-group--prepend">
                        <div class="el-input-group__prepend">{{item.title}}</div>
                        <elAutocomplete v-model="item.value[item.name].value" :placeholder="item.ph"></elAutocomplete>
                    </div>
                </el-col> -->
                <el-col v-if="item.type == 'radio' && item.isAutocomplete == false" :span="6" class="pb15">
                    <div class="el-input el-input-group el-input-group--prepend">
                        <div class="el-input-group__prepend">{{item.title}}</div>
                        <el-select v-model="item.value[item.name].value" clearable>
                            <el-option
                                v-for="i in item.option"
                                :key="i.label"
                                :label="i.label"
                                :value="i.value">
                            </el-option>
                        </el-select>
                    </div>
                </el-col>
                <!-- 多级选择框类型 -->
                <el-col v-if="item.type == 'cascader' && item.isAutocomplete == false" :span="8" class="pb15">
                    <div class="el-input el-input-group el-input-group--prepend">
                        <div class="el-input-group__prepend">{{item.title}}</div>
                        <el-cascader
                            :placeholder="item.ph"
                            :options="item.options"
                            v-model="selectedOptions"
                            change-on-select
                            clearable
                            @change="handleSelectedOptions">
                        </el-cascader>
                    </div>
                </el-col>
                <!-- input框类型 -->
                <el-col v-if="item.type == 'text'" :span="8" class="pb15">
                    <el-input :placeholder="item.ph" v-model="item.value[item.name].value">
                        <template slot="prepend">{{item.title}}</template>
                    </el-input>
                </el-col>
                <!-- 日期时间范围类型 -->
                <el-col v-if="item.type == 'daterange'" :span="8" class="pb15">
                    <div class="el-input el-input-group el-input-group--prepend">
                        <div class="el-input-group__prepend">{{item.title}}</div>
                        <el-date-picker
                            v-model="addtime_range"
                            type="daterange"
                            align="right"
                            :placeholder="item.ph"
                            :picker-options="pickerOptions1">
                        </el-date-picker>
                    </div>
                </el-col>
            </div>
            <!-- 按钮 -->
            <el-col :span="8" class="pb15">
                <el-button type="primary" @click="handleSearch">查询</el-button>
                <el-button type="primary" @click="handleAdd">新增</el-button>
            </el-col>
        </el-row>

        <!-- table列表 -->
        <el-row :gutter="24" class="pb15">
            <el-col :span="24">
                <el-table :data="items" border style="width:100%;">
                    <template v-for="(tableItem, tableIndex) in config.tableConfig">
                        <el-table-column :property="tableItem.name" :label="tableItem.title" :width="tableItem.width"></el-table-column>
                    </template>
                    <el-table-column label="操作">
                        <template scope="scope">
                            <el-button size="small" @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
                            <el-button size="small" type="danger" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </el-col>
        </el-row>

        <!-- 分页 -->
        <el-row :gutter="24" style="padding: 5px 0;">
            <el-col :span="24" style="text-align: right;">
                <div class="block">
                    <el-pagination
                        @size-change="handleSizeChange"
                        @current-change="handleCurrentChange"
                        :current-page="pagination.current_page"
                        :page-sizes="[10, 20, 30, 40]"
                        :page-size="pagination.per_page"
                        layout="total, sizes, prev, pager, next, jumper"
                        :total="pagination.total">
                    </el-pagination>
                </div>
            </el-col>
        </el-row>

        <!-- 详情弹窗 -->
        <!-- <detailDialog :item="item"></detailDialog> -->
    </el-row>
</template>

<script>
    import ajax from 'admin/api/ajax';
    import { listConfig } from 'admin/assets/js/templateConfig';
    // import elAutocomplete from '../../../../components/elAutocomplete';
    // import ErrorTip from '../ErrorTip';

    export default {
        data() {
            return{
                notAccess:false,
                config:listConfig,
                searchelemt:[],
                tableelemt:[],
                items:[],
                item:{},

                addtime_range:['', ''],
                pagination: {
                    total:0,
                    per_page:10,
                    from:1,
                    to:0,
                    current_page:1
                },
                selectedOptions:[]
            }
        },
        mounted() {
            this._initSearchElemt();
            this.fetchItems(this.pagination.current_page);
        },
        watch: {
            addtime_range(newVal, oldVal) {
                for(let i in this.searchelemt) {
                    let item = this.searchelemt[i];
                    if(this.searchelemt[i].type == 'daterange') {
                        this.searchelemt[i].value[item.name].value.sdate = this._formatDate(newVal[0]);
                        this.searchelemt[i].value[item.name].value.edate = this._formatDate(newVal[1]);
                        if(this.searchelemt[i].value[item.name].value.sdate == '1970-1-1' && this.searchelemt[i].value[item.name].value.edate == '1970-1-1') {// 点击选择时取消了选择而产生的1970-1-1，需要置空
                            this.searchelemt[i].value[item.name].value.sdate = '';
                            this.searchelemt[i].value[item.name].value.edate = '';
                        }
                    }
                }
            },
            $route(newRoute) {
                // 监听该页面路由变化
                if(newRoute.name == this.config.route.name) {
                    this.fetchItems(this.pagination.current_page);
                }
            }
        },
        // computed的数据是没有双向绑定的
        computed: {
            pickerOptions1() {
                for(let i in this.searchelemt) {
                    // 如果有时间日期范围选择器，则遍历
                    if(this.searchelemt[i].type == 'daterange') {
                        return {
                            shortcuts: [{
                                text: '最近一周',
                                onClick(picker) {
                                    const end = new Date();
                                    const start = new Date();
                                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                                    picker.$emit('pick', [start, end]);
                                }
                            }, {
                                text: '最近一个月',
                                onClick(picker) {
                                    const end = new Date();
                                    const start = new Date();
                                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                                    picker.$emit('pick', [start, end]);
                                }
                            }, {
                                text: '最近三个月',
                                onClick(picker) {
                                    const end = new Date();
                                    const start = new Date();
                                    start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                                    picker.$emit('pick', [start, end]);
                                }
                            }]
                        } 
                    }
                }
            }
        },
        methods: {
            // 渲染搜索元素
            _initSearchElemt() {
                let searchelemt = []
                if(this.config.searchConfig.length) {
                    for(let item of this.config.searchConfig) {
                        if(item.type == 'text') {
                            searchelemt.push(item);
                        }
                        if(item.type == 'radio') {
                            searchelemt.push(item);
                        }
                        if(item.type == 'cascader') {
                            searchelemt.push(item);
                        }
                        if(item.type == 'daterange') {
                            searchelemt.push(item);
                        }
                    }
                }
                this.searchelemt = searchelemt;
            },
            // 查
            handleSearch() {
                this.fetchItems(this.pagination.current_page);
            },
            // 增
            handleAdd() {
                let path = this.config.route.modifyPath;
                this.$router.push({ 
                    path: path
                    // query:{
                    //     tabName: `编辑${this.config.title}`,
                    //     btn_flag: true // 若在本页通过按钮点击跳转页面，则重置跳转的页面状态
                    // }
                });
            },
            // 删
            handleDelete(index, row) {
                this.$confirm('确定删除该条数据吗?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type:'warning'
                }).then(()=>{
                    let params = {};
                    params.id = row.id;
                    ajax.ajaxGet(this.config.deleteUrl, params, (response)=>{
                        if(response.body.retcode == 0 && response.body.data != '') {
                            this.$message('删除成功');
                            this.fetchItems(this.pagination.current_page);
                        }else{
                            this.$message.error('删除失败');
                        }
                    })
                }).catch(()=>{
                    // do nothing
                })
            },
            // 改
            handleEdit(index, row) {
                let path = this.config.route.modifyPath;
                let id = row.id;
                this.$router.push({ 
                    path: path,
                    query:{
                        id: id,
                        tabName: `编辑${this.config.title}`,
                        btn_flag: true // 若在本页通过按钮点击跳转页面，则重置跳转的页面状态
                    }
                })
            },
            // 详情
            handleDetail(index, row) {
                this.item = row;
            },
            // 分页-页数改变
            handleSizeChange(val) {
                this.pagination.per_page = val;
                this.fetchItems(this.pagination.current_page);
            },
            // 分页-当前页数改变
            handleCurrentChange(val) {
                this.pagination.current_page = val;
                this.fetchItems(this.pagination.current_page);
            },
            // 日期格式化
            _formatDate(strTime, type) {
                const date = new Date(strTime);
                let mydate = date.getFullYear()+"-"+this._appendZero((date.getMonth()+1))+"-"+this._appendZero(date.getDate());
                if(type == 'datetime') {
                    mydate += " "+this._appendZero(date.getHours())+":"+this._appendZero(date.getMinutes())+":"+this._appendZero(date.getSeconds());
                }else if(type == 'time'){
                    mydate = this._appendZero(date.getHours())+":"+this._appendZero(date.getMinutes())+":"+this._appendZero(date.getSeconds());
                }
                return mydate;
            },
            // 日期格式化-补零
            _appendZero(val) {
                if(val < 10) {
                    return `0${val}`;
                }else{
                    return val;
                }
            },
            // 多级选择框赋值
            handleSelectedOptions(val) {
                // for(let item of this.config.searchConfig) {
                //     if(item.type == 'cascader') {
                //         // let j = 0;
                //         // for(let i in item.value) {
                //         //     console.log(this.search,i,'this.search[i]')
                //         //     if(val[j]) {
                //         //         this.search[i].value = val[j];
                //         //     }
                //         //     ++j;
                //         // }
                //         // if(val[0]) {
                //         //     this.search.offend_type2.value = val[0];
                //         // }else{
                //         //     this.search.offend_type2.value = '';
                //         // }
                //         // if(val[1]) {
                //         //     this.search.offend_type3.value = val[1];
                //         // }else{
                //         //     this.search.offend_type3.value = '';
                //         // }
                //     }
                // }
            },
            fetchItems(page) {
                if(!this.config.itemsUrl) {
                    onsole.log("未设置查询地址(itemsUrl)");
                    return false;
                }
                
                // 查询条件
                let data = {};
                data.page = page;
                data.limit = this.pagination.per_page;
                data.search = {};
                
                let searchArr = {};
                for(let index in this.searchelemt) {
                    let name = this.searchelemt[index].name
                    searchArr[name] = this.searchelemt[index].value
                }
                for(var i in searchArr) {
                    var dval = searchArr[i];
                    if(dval[i].value) {
                        data.search[i] = dval[i];
                    }
                }

                // 查询显示数据
                ajax({
                  url: this.config.itemsUrl,
                  method: 'post',
                  data: data})
                  .then((response)=>{
                    if(response && response.body && response.body.retcode == '0') {
                        this.items = response.body.data.data;// 列表赋值
                        for(let i in this.pagination) {// 分页赋值
                            this.pagination[i] = response.body.data[i];
                        }
                        // 如果有字段是数组并且要显示出来的，则处理一下
                        for(let j in this.config.modifyConfig) {
                            let item = this.config.modifyConfig[j];
                            if(item.dataType == 'array') {
                                for(let m in this.items) {
                                    if(this.items[m][item.name] && this.items[m][item.name] instanceof Array) {
                                        let value = '';
                                        for(let n in this.items[m][item.name]) {
                                            value += this.items[m][item.name][n].value + ', '
                                        }
                                        let val = value.substr(0, value.length-2);
                                        this.items[m][item.name] = val;
                                    }
                                }
                            }
                        }
                        if(response.body.menuRight) {
                            this.menuRight = response.body.menuRight;
                        }
                        if(response.body.nick) {
                            this.nick = response.body.nick;
                        }
                    }
                })
            }
        },
        components: {
            // elAutocomplete,
            // ErrorTip
        }
    }
</script>

<style>
    .pb15{
        padding-bottom: 15px;
    }
    .el-input-group>.el-input__inner{
        display: block;
    }
    .el-range-editor--small .el-range-input,
    .el-range-editor--small .el-range-separator{
        vertical-align: top;
    }
    /* .m-detail-color{
        color:#20a0ff;
        cursor: pointer;
    }
    .m-title-detail{
        height:40px;
        line-height: 40px;
        font-size: 16px;
        background-color: rgb(235, 235, 235);
        margin-bottom: 10px;
        margin-top: 20px;
        padding:0 10px;
    }
    .m-dialog .el-col{
        padding:0 10px;
    }
    .m-dialog .el-dialog__body{
        padding-top: 0;
    }
    .m-imgBox{
        width:200px;
        height:auto;
        border-radius: 5px;
        float:left;
        margin-right: 10px;
        margin-bottom: 10px;
    } */
</style>