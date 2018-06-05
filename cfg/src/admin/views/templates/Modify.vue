<!-- 修改模板 -->
<template>
    <el-row>
        <el-row :gutter="20" class="m-form-wrapper">
            <el-form :model="form" :rules="formRule" ref="rulesForm" label-width="130px">
                <el-row v-for="(category,cindex) in config.modifyConfig.categorys" :key="cindex">
                    <el-col :span="24" class="m-title">
                        <span class="m-h3">{{category}}</span>
                        <template v-for="item in elemts"  v-if="elemts.length > 0">
                            <template v-if="item.type == 'table'">
                                <el-button v-if="category == item.category" type="primary" @click="handleTableAdd(category)" style="float:right; margin-top:2px;">添加</el-button>
                            </template>
                        </template>
                    </el-col>
                    <template v-for="elemtItem in elemts" v-if="elemts.length > 0">
                        <template v-if="elemtItem.category == category">
                            <!-- input框-混合类型-有增加删除按钮类型 -->
                            <el-col v-if="elemtItem.type == 'multiinput' && !elemtItem.noRender" :span="elemtItem.width" style="position:relative;">
                                <el-form-item 
                                    v-for="(arrayItem, arrayIndex) in form[elemtItem.name]"
                                    :key="arrayIndex"
                                    :label="elemtItem.title"
                                    :prop="[elemtItem.name] + '.' + arrayIndex + '.value'"
                                    :rules="{}">
                                    <el-input v-model="arrayItem.value"></el-input>
                                    <div style="position:absolute; right:-80px; top:0;">
                                        <el-button :disabled="form[elemtItem.name].length <= 1" @click.prevent="handleMixDelete(arrayItem, elemtItem.name)">删除</el-button>
                                    </div>
                                </el-form-item>
                                <el-button @click.prevent="handleMixAdd(elemtItem.name, 'text')" style="position:absolute; right:-160px; bottom:22px;">增加</el-button>
                            </el-col>
                            <!-- input框-普通类型 -->
                            <el-col v-if="elemtItem.type == 'text' && !elemtItem.noRender" :span="elemtItem.width">
                                <el-form-item :label="elemtItem.title" :prop="elemtItem.name">
                                    <el-input v-model="form[elemtItem.name]"></el-input>
                                </el-form-item>
                            </el-col>
                            <!-- select框类型 -->
                            <el-col v-if="elemtItem.type == 'selectradio' && !elemtItem.noRender" :span="elemtItem.width">
                                <el-form-item :label="elemtItem.title" :prop="elemtItem.name">
                                    <el-select v-model="form[elemtItem.name]" :placeholder="elemtItem.ph" @change="handleSelectChange">
                                        <el-option
                                            v-for="i in elemtItem.options"
                                            :key="i.value"
                                            :label="i.label"
                                            :value="i.value">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <!-- 时间类型 -->
                            <el-col v-if="elemtItem.type == 'date' && !elemtItem.noRender" :span="elemtItem.width">
                                <el-form-item :label="elemtItem.title" :prop="elemtItem.name">
                                    <el-date-picker
                                        v-model="form[elemtItem.name]"
                                        type="date"
                                        :placeholder="elemtItem.ph" 
                                        >
                                    </el-date-picker>
                                </el-form-item>
                            </el-col>
                            <!-- cascader级联选择器类型 --><!--只配置了product archive-->
                            <el-col v-if="elemtItem.type == 'cascader' && !elemtItem.noRender" :span="elemtItem.width">
                                <el-form-item :label="elemtItem.title">
                                    <archiveCascader v-if="elemtItem.name == 'product'" @change="handleProductChange" :type="elemtItem.name" :placeholder="elemtItem.ph" :value="product"></archiveCascader>
                                    <archiveCascader v-if="elemtItem.name == 'archive'" :productid="form.product.service_id" :isLast="false" @change="handleArchiveChange" :type="elemtItem.name" :placeholder="elemtItem.ph" :value="archive"></archiveCascader>
                                </el-form-item>
                            </el-col>
                            <!-- 混合类型-关联类型 包含input和select -->
                            <el-col v-if="elemtItem.type == 'inputSelect' && !elemtItem.noRender" :span="elemtItem.width" style="position:relative;">
                                <div v-for="(arraysItem, arraysIndex) in form[elemtItem.name]" style="position:relative;">
                                    <el-form-item 
                                        :label="elemtItem.title"
                                        :key="arraysIndex"
                                        :prop="[elemtItem.name] + '.' + arraysIndex + '.order_num'"
                                        :rules="formRule[elemtItem.name]">
                                        <el-input v-model="arraysItem.order_num"></el-input>
                                    </el-form-item>
                                    <el-form-item
                                        label=""
                                        :prop="[elemtItem.name] + '.' + arraysIndex + '.order_way'"
                                        :rules="formRule[elemtItem.name]"
                                        style="position:absolute;right:-230px;top:0;">
                                        <el-select v-model="arraysItem.order_way" :placeholder="elemtItem.ph">
                                            <el-option
                                                v-for="i in elemtItem.options"
                                                :key="i.value"
                                                :label="i.label"
                                                :value="i.value">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                    <div style="position:absolute;right:-310px; top:0;">
                                        <el-button :disabled="form[elemtItem.name].length <= 1" @click.prevent="handleMixDelete(arraysItem, elemtItem.name)">删除</el-button>
                                    </div>
                                </div>
                                <el-button @click.prevent="handleMixAdd(elemtItem.name, 'inputSelect')" style="position:absolute; right:-400px; bottom:22px;">增加</el-button>
                            </el-col>
                            <!-- textarea框类型 -->
                            <el-col v-if="elemtItem.type == 'textarea' && !elemtItem.noRender" :span="elemtItem.width">
                                <el-form-item :label="elemtItem.title" :prop="elemtItem.name">
                                    <el-input :type="elemtItem.type" v-model="form[elemtItem.name]"></el-input>
                                </el-form-item>
                            </el-col>
                            <!-- 截图 -->
                            <el-col v-if="elemtItem.type == 'upload' && !elemtItem.noRender" :span="elemtItem.width">
                                <el-form-item :label="elemtItem.title" :prop="elemtItem.name">
                                    <div @click="handlePicClick(elemtItem.name)">
                                        <el-upload
                                            :action="uploadPicUrl"
                                            list-type="picture-card"
                                            multiple
                                            accept=".jpg,.png"
                                            name="upload_file"
                                            :file-list="elemtItem.uploadedData"
                                            :data="uploadHeader"
                                            :on-preview="handlePictureCardPreview"
                                            :on-remove="handlePictureCardRemove"
                                            :before-upload="handleBeforeUpload"
                                            :on-success="handleUploadSuccess">
                                            <i class="el-icon-plus m-icon"></i>
                                        </el-upload>
                                        <el-dialog  size="tiny">
                                            <img width="100%" src="" alt="">
                                        </el-dialog>
                                    </div>
                                </el-form-item>
                            </el-col>
                            <!-- table列表 -->
                            <el-col v-if="elemtItem.type == 'table' && !elemtItem.noRender" :span="elemtItem.width" style="margin-bottom:20px;">
                                <el-table :data="form[elemtItem.name]" border style="width:100%">
                                    <template v-for="column in elemtItem.tableColumns">
                                        <el-table-column :property="column.name" :label="column.title" :width="column.width"></el-table-column>
                                    </template>
                                    <el-table-column label="操作">
                                        <template scope="scope">
                                            <el-button size="small" @click="handleTableEdit(scope.$index, scope.row, category)">编辑</el-button>
                                            <el-button size="small" @click="handleTableDelete(scope.$index, scope.row, category)">删除</el-button>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </el-col>
                        </template>
                    </template>
                </el-row>
                <!-- 富文本编辑器类型 ref唯一 不能放循环里，否则报offsetWidth错误 -->
                <el-form-item label="编辑器">
                    <div ref="editor" class="my_editor_content" style="height: 200px;"></div>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSubmit">保存</el-button>
                </el-form-item>
            </el-form>
        </el-row>

        <!-- table列表-添加和编辑弹窗 -->
        <modifyDialog 
            v-model="dialogVisible" 
            :title="dialogTitleText" 
            :item="dialogItem" 
            :tableListConfig="dialogElemts"
            @close="handleDialogClosed" 
            @submit="handleDialogSubmit">
        </modifyDialog>
        <!-- 图片预览 -->
        <Lightbox :images="lightBoxImgs" ref="lightbox"></Lightbox>
    </el-row>
</template>

<script>
    import ajax from 'admin/api/ajax';
    import { listConfig } from 'admin/assets/js/templateConfig';
    import archiveCascader from 'admin/components/archiveCascader';
    import Lightbox from 'admin/components/lightbox';
    import modifyDialog from './ModifyDialog';

    export default {
        data() {
            return {
                config:listConfig,
                elemts:[],
                form:{},
                product:[],// 相关产品
                archive:[],// 问题归档

                dialogTitleText:'',
                dialogVisible:false,
                dialogElemts:{},
                dialogItem:{},
                dialogItemIndex:'',

                uploadHeader:{csrf_token_key:''},
                // fileListData:[],
                uploadPicUrl: window.base_url+'admin/imageUpload/upload/', //图片上传
                delImageUrl: window.base_url + 'admin/Dataclear_common/deleteImageJson/merchant/proof_photo/', //图片删除
                lightBoxImgs:[],
                uploadingPicFlag:'',

                editor:'',
                ueConfig:{// 富文本工具配置
                    toolbars: [
                        ['bold', 'italic', 'underline','fontsize', 'removeformat', 'formatmatch', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist',
                            'insertunorderedlist', 'cleardoc', 'simpleupload','insertimage','inserttable','link','unlink', 'fullscreen','preview', 'source', 'undo', 'redo']
                    ]
                }
            }
        },
        mounted() {
            this._initConfigElemt();
            this._initConfigForm();

            if(this.$route.query.btn_flag) {// 点击按钮进来则清空数据
                this._initConfigForm();
                if(this.$route.query.id) {
                    this._fetchItem();
                }
            }
        },
        watch: {
            form(newVal, oldVal) {
                this.form = newVal;
            },
            $route(newRoute) {
                // 监听该页面路由变化
                if(newRoute.name == this.config.route.modifyName) {
                    if(this.$route.query.btn_flag) {// 点击按钮进来则清空数据
                        this._initConfigForm();
                    }
                    if(this.$route.query.id) {
                        this._fetchItem();
                    }
                }
            }
        },
        // computed的数据是没有双向绑定的
        computed: {
            formRule() {
                let formRule = {};
                for(let item of this.config.modifyConfig.elemts) {
                    formRule[item.name] = [];
                    // 是否必填
                    if(item.isMust) {
                        let message = '请输入' + item.title;
                        formRule[item.name].push({required:true, message:message, trigger:'blur'});
                    }
                    // 验证条件
                    if(item.checkType) {
                        if(item.checkType == 'mobile') {// 手机
                            formRule[item.name].push({type:'string', message:'手机号码格式不对', pattern:/^1[34578]\d{9}$/, trigger:'blur'})
                        }else if(item.checkType == 'idcard') {// 身份证
                            formRule[item.name].push({type:'string',message: '身份证格式不对', pattern:/(^\d{15}$)|(^\d{17}([0-9]|X)$)/ ,trigger:'blur'})
                        }else if(item.checkType == 'number'){// 数字
                            formRule[item.name].push({type:'string', message:'只能输入数字', pattern:/^[0-9]*$/, trigger:'blur'})
                        }else if(item.checkType == 'age'){// 年龄
                            formRule[item.name].push({type:'string', message: '请输入正确的年龄',pattern:/^\d{1,3}$/, trigger:'blur'})
                        }else if(item.checkType == 'numberOrLetter'){// 数字或字母
                            formRule[item.name].push({type:'string', message:'只能输入数字或字母', pattern:/^[0-9a-zA-Z]*$/, trigger:'blur'})
                        }
                    }
                    if(!formRule[item.name].length) { // 空数组则删掉
                        delete formRule[item.name];
                    }
                }
                console.log(formRule,'==========校验规则formRule==========')
                return formRule;
            }
        },
        methods: {
            // 渲染表单元素
            _initConfigElemt() {
                let elemts = this.config.modifyConfig.elemts;
                if(elemts && elemts.length) {
                    for(let i = 1; i <= elemts.length; ++i) {
                        for(let item of elemts) {
                            if(i == item.id) {// 按id顺序配置表单元素
                                this.elemts.push(item)
                            }
                        }
                    }
                }
                console.log(this.elemts, '==========渲染表单elemts=============')
            },
            // 初始化form结构，清空表单
            _initConfigForm() {
                let form = {};
                let elemts = this.config.modifyConfig.elemts;
                for(let item of elemts) {
                    if(item.name) {
                        // if(item.type == 'upload') {// 单独清空图片
                        //     item.value = [];
                        //     item.uploadedData = item.value;
                        // }
                        // if(item.name == 'gzhlist') {
                        //     item.value = [];
                        // }
                        // if(item.name == 'moumoulist') {
                        //     item.value = [];
                        // }
                        if(item.type == 'cascader' && item.name == 'product') {
                            this.product = [];
                        }
                        if(item.type == 'cascader' && item.name == 'archive') {
                            this.archive = [];
                        }
                        // if(item.valuetype == 'array') {
                        //     item.value = [];
                        // }
                        // if(item.type == 'multiinput') {
                        //     item.value = [{value:''}];
                        // }
                        // if(item.dataType == 'inputSelect') {
                        //     item.value = [{order_num:'', order_way:''}];
                        // }
                        if(item.type == 'editor') {
                            if(this.editor) {
                                return;
                            }
                            this.$nextTick(function() {
                                // 保证 this.$el 已经插入文档
                                let id = "editor_remark";
                                this.$refs.editor.id = id;
                                this.editor = UE.getEditor(id, this.ueConfig);
                                const _this = this;
                                this.editor.ready(function(){
                                    _this.editor.setContent(_this.form[item.name]);
                                })
                            })
                        }
                        form[item.name] = item.value;
                    }
                }
                this.form = form;
                console.log(this.form, '===========清空表单form==============')
            },
            // 填充表单
            _setFormVal(data) {
                for(let i in this.form) {// 根据form中有的属性来赋值
                    this.form[i] = data[i];

                    if(i == 'product') {// 转成数组显示多级选择器
                        this.product[0] = this.form[i].bg_id;//上一级菜单
                        this.product[1] = this.form[i].service_id;//选中菜单
                    }
                    if(i == 'archive') {// 转成数组显示多级选择器
                        this.archive[0] = this.form[i].first_dir_code;//上一级菜单
                        if(this.form[i].second_dir_code) {
                            this.archive[1] = this.form[i].second_dir_code;//选中菜单
                        }
                    }
                }
                for(let item of this.config.modifyConfig.elemts) {
                    if(item.type == 'upload') {
                        if(this.form[item.name] && this.form[item.name].length) {
                            item.uploadedData = [];
                            this.lightBoxImgs = [];
                            for(let i in this.form[item.name]) {
                                const imgUrl = `${window.base_url}admin/accessFile/output_image/${this.form[item.name][i]}`;
                                this.lightBoxImgs.push({
                                    src:imgUrl,
                                    thumb:imgUrl,
                                    caption:''
                                });
                                item.uploadedData.push({
                                    name:this.form[item.name][i],
                                    url:imgUrl
                                });
                                console.log(item.uploadedData,'uploadedData')
                            }
                        }
                    }
                    if(item.type == 'editor') {
                        this.$nextTick(function() {
                            if(this.editor) {
                                const _this = this;
                                this.editor.ready(function(){
                                    _this.editor.setContent(_this.form[item.name]);
                                })
                            }
                        })
                    }
                }
                console.log(this.form, '===========填充表单form==============')
            },
            
            // 混合表单类型(输入框选择框按钮混合)-增加元素
            handleMixAdd(itemName, type) {
                if(type == 'text') {
                    this.form[itemName].push({
                        value:''
                        // key:Date.now()
                    })
                }else if(type == 'inputSelect'){
                    let obj = {};
                    for(let i in this.form[itemName][0]) {
                        obj[i] = '';
                    }
                    this.form[itemName].push(obj)
                }
            },
            // 混合表单类型(输入框选择框按钮混合)-删除元素
            handleMixDelete(item, itemName) {
                var index = this.form[itemName].indexOf(item);
                if(this.form[itemName].length >= 2) {
                    if(index !== -1) {
                        this.form[itemName].splice(index, 1);
                    }
                }
            },
            // 单选框
            handleSelectChange(val) {
                // console.log(val,'val')
            },
            // 多选框-选择产品
            handleProductChange(val, val_zh) {
                let arr = val_zh.split(" ");
                if(val && val.length > 0){
                    this.form.product.bg_id = val[0];
                    this.form.product.service_id = val[1];
                    this.form.product.service_zh = arr[1];
                }else{
                    this.form.product.bg_id = '';
                    this.form.product.service_id = '';
                    this.form.product.service_zh = '';
                }
            },
            // 多选框-选择归档路径
            handleArchiveChange(val){
                if(val && val.length > 0){
                    if(val[1]){
                        this.form.archive.first_dir_code = val[0];
                        this.form.archive.second_dir_code = val[1];
                        this.form.archive.archive_path = val[1];
                    }else{
                        this.form.archive.first_dir_code = val[0];
                        this.form.archive.archive_path = val[0];
                    }
                }else{
                    this.form.archive.first_dir_code = '';
                    this.form.archive.second_dir_code = '';
                    this.form.archive.archive_path = '';
                }
            },
            // table-添加数据
            handleTableAdd(category) {
                for(let item of this.config.modifyConfig.elemts) {
                    if(item.type == 'table' && item.category == category) {
                        this.dialogElemts = item;
                    }
                }
                this.dialogVisible = true;
                this.dialogTitleText = '添加信息';
                this.dialogItem = {};// 点击添加传给弹框一个空值，清空对话框内容
                this.dialogItemIndex = '';// 如果上一步操作是编辑，此时要清空this.dialogItemIndex，不然会一直是修改上一条数据
            },
            // table-编辑数据
            handleTableEdit(index, row, category) {
                for(let item of this.config.modifyConfig.elemts) {
                    if(item.type == 'table' && item.category == category) {
                        this.dialogElemts = item;
                    }
                }
                this.dialogVisible = true;
                this.dialogTitleText = '编辑信息';
                this.dialogItem = row;
                this.dialogItemIndex = index;
            },
            // table-删除数据
            handleTableDelete(index, row, category){
                for(let item of this.config.modifyConfig.elemts) {
                    if(item.type == 'table' && item.category == category) {
                        this.$confirm('确定删除该条数据吗?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type:'warning'
                        }).then(()=>{
                            let params = {};
                            if(row.id) {// 有ID，则数据已保存在数据库，调用接口删除
                                params.id = row.id;
                                AjaxApi.ajaxGet(this.config.deleteTableUrl, params, (response)=>{
                                    if(response.body.retcode == 0 && response.body.data) {
                                        this.$message('删除成功');
                                        this.form[item.name].splice(index, 1);
                                    }else{
                                        this.$message.error('删除失败');
                                    }
                                })
                            }else if(!row.id && index){// 没有ID但有index，则数据还没有保存在数据库里，本地删除table数据即可
                                this.form[item.name].splice(index, 1);
                            }
                        }).catch(()=>{
                            // do nothing
                        })
                    }
                }
            },
            // table-对话框组件-关闭
            handleDialogClosed(){
                this.dialogVisible = false;
            },
            // table-对话框组件-提交数据
            handleDialogSubmit(dialogformData, categoryItem) {
                this.dialogVisible = false;
                if(this.dialogItemIndex !== '') {// 有ID，编辑记录
                    for(let i in dialogformData) {
                        if(!dialogformData[i]) {
                            dialogformData[i] = '';//防止传字符串undefined
                        }
                        this.form[categoryItem.name][this.dialogItemIndex][i] = dialogformData[i];
                    }
                }else{// 没有ID，添加记录
                    for(let i in dialogformData) {
                        if(!dialogformData[i]) {
                            dialogformData[i] = '';//防止传字符串undefined
                        }
                    }
                    if(this.form[categoryItem.name] == undefined) {
                        this.form[categoryItem.name] = [];
                    }
                    this.form[categoryItem.name].push(dialogformData)
                }
                
            },
            // 提交表单
            handleSubmit() {
                this.$refs.rulesForm.validate((valid)=>{
                    if(valid) {
                        let params = {};
                        if(this.form.id) {
                            params.id = this.form.id
                        }
                        if(window._token) {
                            params.csrf_token_key = window._token.value;
                        }
                        for(let item of this.config.modifyConfig.elemts) {
                            if(item.type == 'date' && this.form[item.name]) {
                                this.form[item.name] = this._dateFormat(this.form[item.name]);
                            }
                            if(item.type == 'editor') {
                                this.form[item.name] = this.editor.getContent();
                            }
                        }
                        params.field = this.form;

                        console.log(this.form, '=============提交的form==============')
                        AjaxApi.ajaxPost(this.config.modifyUrl, params, (response)=>{
                            if(response.body.token) {
                                window._token = response.body.token;
                            }
                            if(response.body.retcode == 0 && response.body.data) {
                                this.$message({
                                    message: '添加或修改成功',
                                    type:'success'
                                });
                                this.$router.push({
                                    path:this.config.route.path
                                });
                            }else{
                                this.$message({
                                    message:'添加或修改失败',
                                    type:'error'
                                })
                            }
                        })
                    }else{
                        console.log('=========验证不通过===========')
                        return false;
                    }
                })
            },
            handlePicClick(name) {
                this.uploadingPicFlag = name;
            },
            // 图片-上传前
            handleBeforeUpload (file) {
                if(window._token) {
                    this.uploadHeader.csrf_token_key = window._token.value;
                }
                return true;
            },
            // 图片-预览
            handlePictureCardPreview(file) {
                for(let item of this.config.modifyConfig.elemts) {
                    if(item.type == 'upload' && item.name == this.uploadingPicFlag) {
                        let imageKey = 0;
                        for(let i in this.form[item.name]){
                            if(this.form[item.name][i] == file.name){
                                imageKey = i;
                            }
                        }
                        this.$refs.lightbox.showImage(imageKey);
                    }
                }
            },
            // 图片-删除
            handlePictureCardRemove(file, fileList) {
                for(let item of this.config.modifyConfig.elemts) {
                    if(item.type == 'upload' && item.name == this.uploadingPicFlag) {
                        this.$confirm('此操作将永久删除该文件,是否继续?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(() => {
                            let imageKey = 0;
                            for(let i in this.form[item.name]) {
                                if(this.form[item.name][i] == file.name) {
                                    imageKey = i;
                                }
                            }
                            this.form[item.name].splice(imageKey, 1);
                            this._setFormVal(this.form);
                            let params = {};
                            if(window._token) {
                                params.csrf_token_key = window._token.value
                            }
                            params.imageKey = imageKey
                            if(this.form.id) {
                                params.id = this.form.id
                            }
                            AjaxApi.ajaxPost(this.delImageUrl, params, (response)=>{
                                if(response.body.token) {
                                    window._token = response.body.token;
                                }
                                if(response.body.retcode == 0) {
                                    this.$message('操作成功');
                                }else{
                                    this.$message('操作失败');
                                }
                            });
                        }).catch(() => {
                            // 取消删除图片
                            item.uploadedData = [];
                            for(let i in this.form[item.name]) {
                                item.uploadedData.push({
                                    name:this.form[item.name][i],
                                    url:`${window.base_url}admin/accessFile/output_image/${this.form[item.name][i]}`
                                });
                            }
                        });
                        return false;
                    }
                }
            },
            // 图片-提交成功
            handleUploadSuccess(response, file, fileList) {
                if(response.token) {
                    window._token = response.token;
                }
                if(response.retcode == 0) {
                    for(let item of this.config.modifyConfig.elemts) {
                        if(item.type == 'upload' && item.name == this.uploadingPicFlag) {
                            if(!this.form[item.name]) {
                                this.form[item.name] = [];
                            }
                            this.form[item.name].push(response.name);
                        }
                    }
                    this._setFormVal(this.form);// 将上传成功的图片保存到预览图数组中，这会导致不能选择多张上传！！！
                    this.$message('上传成功');
                }else{
                    this.$message.error('上传失败');
                }
            },
            // 修改-通过路由获取修改的id
            _fetchItem() {
                if(this.$route.query.id) {
                    this.form.id = this.$route.query.id;
                    var params = {};
                    params.id = this.form.id;
                }

                AjaxApi.ajaxGet(this.config.itemUrl, params, (response)=>{
                    if(response.body.retcode == 0 && response.body.data) {
                        this._setFormVal(response.body.data);
                    }
                })
            },
            // 格式化日期
            _dateFormat(dateFomatter){
                if(dateFomatter && dateFomatter != '0000-00-00 00:00:00') {
                    let date = new Date(dateFomatter);
                    let y = date.getFullYear();
                    let m = date.getMonth() + 1;
                    m = m < 10 ? '0' + m : m;
                    let d = date.getDate();
                    d = d < 10 ? '0' + d : d;
                    return y + '-' + m + '-' + d;
                } else {
                    return '';
                }
            },
        },
        components: {
            archiveCascader,
            modifyDialog,
            Lightbox
        },
        // 如果有编辑器，则页面不缓存，关闭或切换时执行该方法销毁编辑器实例
        destroyed(){
            if(this.editor){
                this.editor.destroy();
                this.editor = '';
            }
        }
    }
</script>

<style>
    .m-form-wrapper{
        padding:0 15px;
    }
    .m-title{
        height:36px;
        line-height: 36px;
        font-size: 16px;
        background-color: rgb(235, 235, 235);
        margin-bottom: 20px;
    }
    .m-h3{
        margin-left: 10px;
    }
    .m-el-input__inner{
        width:120px;
    }
    .m-icon{
        margin-left: 0 !important;
    }
    .my_editor_content{
        
    }
    .my_editor_content .edui-toolbar{
        height: 25px !important;
    }
    /*.title{
        height:36px;
        line-height: 36px;
    }
    .details{
        color:#4db3ff;
        cursor:pointer;
    }*/
</style>