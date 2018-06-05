<template>
    <el-row>
        <el-dialog :title="title" v-model="dialogVisible" :close-on-click-modal="false" size="large" class="m-dialog" @close="handleClosed">
            <el-form :model="form" :rules="formRule" ref="rulesForm" label-width="130px">
                <template v-for="elemtItem in elemts" v-if="elemts.length > 0">
                    <!-- 混合类型-input框-有增加删除按钮类型 -->
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
                    <!-- 单选框类型 -->
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
                    <!-- 混合类型-关联类型 包含输入框和选择框 -->
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
                    <!-- 长文本框类型 -->
                    <el-col v-if="elemtItem.type == 'textarea' && !elemtItem.noRender" :span="elemtItem.width">
                        <el-form-item :label="elemtItem.title" :prop="elemtItem.name">
                            <el-input :type="elemtItem.type" v-model="form[elemtItem.name]"></el-input>
                        </el-form-item>
                    </el-col>
                    <!-- 截图 -->
                    <el-col v-if="elemtItem.type == 'upload' && !elemtItem.noRender" :span="elemtItem.width">
                        <el-form-item :label="elemtItem.title" :prop="elemtItem.name">
                            <el-upload
                                :action="uploadPicUrl"
                                list-type="picture-card"
                                multiple
                                accept=".jpg,.png"
                                name="upload_file"
                                :file-list="fileListData"
                                :data="uploadHeader"
                                :on-preview="handlePictureCardPreview"
                                :on-remove="handlePictureCardRemove"
                                :before-upload="handleBeforeUpload"
                                :on-success="handleUploadSuccess"
                                >
                                <i class="el-icon-plus m-icon"></i>
                            </el-upload>
                            <el-dialog  size="tiny">
                                <img width="100%" src="" alt="">
                            </el-dialog>
                        </el-form-item>
                    </el-col>
                </template>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="handleCancel">取消</el-button>
                <el-button type="primary" @click="handleSubmit">确定</el-button>
            </div>

            <!-- 图片预览 -->
            <Lightbox :images="lightBoxImgs" ref="lightbox"></Lightbox>
        </el-dialog>
    </el-row>
</template>

<script>
    import { listConfig } from 'admin/assets/js/templateConfig';
    import Lightbox from 'admin/components/lightbox';

    export default {
        data() {
            return{
                config:listConfig,
                elemts:[],
                form:{},

                dialogVisible:false,

                uploadHeader:{csrf_token_key:''},
                fileListData:[],
                uploadPicUrl: window.base_url+'admin/imageUpload/upload/', //图片上传
                delImageUrl: window.base_url + 'admin/Dataclear_common/deleteImageJson/merchant/proof_photo/', //图片删除
                lightBoxImgs:[]
            }
        },
        props: {
            // 单条数据
            item: {
                type: Object,
                default: {}
            },
            //显示和隐藏
            value: {
                type: Boolean,
                default: false
            },
            // 弹窗标题
            title: {
                type: String,
                default:'编辑信息'
            },
            // 配置中的整个table数据
            tableListConfig: {
                type: Object,
                default: []
            }
        },
        mounted() {
            
        },
        watch: {
            value(newVal, oldVal) {
                this.dialogVisible = newVal;

                if(newVal) {// 对话框显示
                    this._initConfigElemt();
                    this._initConfigForm();
                    this._setFormVal(this.item);
                }else{// 对话框关闭
                    this.handleClosed();
                }
            }
        },
        methods: {
            // 渲染对话框表单元素
            _initConfigElemt() {
                let elemts = this.tableListConfig.dialogElemts;
                if(elemts && elemts.length) {
                    for(let i = 1; i <= elemts.length; ++i) {
                        for(let item of elemts) {
                            if(i == item.id) {// 按id顺序配置表单元素
                                this.elemts.push(item)
                            }
                        }
                    }
                }
                // console.log(this.elemts, '==========对话框-渲染表单elemts=============')
            },
            // 初始化form结构，清空表单
            _initConfigForm() {
                let form = {};
                let elemts = this.tableListConfig.dialogElemts;
                if(elemts && elemts.length) {
                    for(let item of elemts) {
                        form[item.name] = item.value;
                        if(item.name == 'proof_photo') {// 清空图片
                            item.value = [];
                            this.fileListData = item.value;
                        }
                    }
                }
                this.form = form;
                // console.log(this.form, '===========对话框-清空表单 form==============')
            },
            // 填充表单
            _setFormVal(data) {
                for(let i in this.form) {// 根据form中有的属性来赋值
                    this.form[i] = data[i];
                }
                if(this.form.proof_photo && this.form.proof_photo.length) {
                    this.fileListData = [];
                    this.lightBoxImgs = [];
                    for(let i in this.form.proof_photo) {
                        const imgUrl = `${window.base_url}admin/accessFile/output_image/${this.form.proof_photo[i]}`;
                        this.lightBoxImgs.push({
                            src:imgUrl,
                            thumb:imgUrl,
                            caption:''
                        });
                        this.fileListData.push({
                            name:this.form.proof_photo[i],
                            url:imgUrl
                        });
                    }
                }
                // console.log(this.form, '===========对话框-填充表单form==============')
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
            handleCancel() {
                this.handleClosed();
            },
            // 点击右上角或触发弹窗关闭时，清空数据
            handleClosed(){
                this._initConfigForm();
                this.elemts = [];
                this.$emit('close');
            },
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
                        for(let i in this.tableListConfig.dialogElemts) {
                            let item = this.tableListConfig.dialogElemts[i];
                            if(item.type == 'date' && this.form[item.name]) {
                                this.form[item.name] = this._dateFormat(this.form[item.name]);
                            }
                        }
                        params.field = this.form;

                        console.log(this.form, '=============对话框-提交的form==============')
                        this.$emit('submit', this.form, this.tableListConfig);
                    }else{
                        console.log('=========验证不通过===========')
                        return false;
                    }
                })
            },
            // 图片-上传前
            handleBeforeUpload(file) {
                if(window._token) {
                    this.uploadHeader.csrf_token_key = window._token.value;
                }
                return true;
            },
            // 图片-预览
            handlePictureCardPreview(file) {
                let imageKey = 0;
                for(let i in this.form.proof_photo){
                    if(this.form.proof_photo[i] == file.name){
                        imageKey = i;
                    }
                }
                this.$refs.lightbox.showImage(imageKey);
            },
            // 图片-删除
            handlePictureCardRemove(file, fileList) {
                this.$confirm('此操作将永久删除该文件,是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    let imageKey = 0;
                    for(let i in this.form.proof_photo) {
                        if(this.form.proof_photo[i] == file.name) {
                            imageKey = i;
                        }
                    }
                    this.form.proof_photo.splice(imageKey, 1);
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
                    this.fileListData = [];
                    for(let i in this.form.proof_photo) {
                        this.fileListData.push({
                            name:this.form.proof_photo[i],
                            url:`${window.base_url}admin/accessFile/output_image/${this.form.proof_photo[i]}`
                        });
                    }
                });
                return false;
            },
            // 图片-提交成功
            handleUploadSuccess(response, file, fileList) {
                if(response.token) {
                    window._token = response.token;
                }
                if(response.retcode == 0) {
                    if(!this.form.proof_photo) {
                        this.form.proof_photo = [];
                    }
                    this.form.proof_photo.push(response.name);
                    this._setFormVal(this.form);// 将上传成功的图片保存到预览图数组中
                    this.$message('上传成功');
                }else{
                    this.$message.error('上传失败');
                }
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
            }
        },
        // computed的数据是没有双向绑定的
        computed: {
            formRule() {
                let formRule = {};
                let elemts = this.tableListConfig.dialogElemts;
                if(elemts && elemts.length) {
                    for(let item of elemts) {
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
                }
                // console.log(formRule,'==========对话框-校验规则formRule==========')
                return formRule;
            }
        },
        components: {
            Lightbox
        }
    }
</script>

<style>
    .m-dialog .el-dialog__body{
        padding: 30px 20px;
        color: #48576a;
        font-size: 14px;
    }
</style>