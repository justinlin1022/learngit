
/**
 * 页面配置所需数据
 * 
 * ph   placeholder
 * isMust   是否必填
 * type   元素类型：text,selectradio,textarea,date,upload,inputSelect,multiinput,table,cascader(product,archive)
 */
export const listConfig = {
    title:'上门卷宗',
    route:{
        name:'listConfig',
        path:'/admin/listConfig',
        modifyName:'modifyConfig',
        modifyPath:'/admin/modify/'
    },
    itemsUrl:`${window.base_url}/common/getList/visit`,//展示列表数据
    itemUrl: `${window.base_url}/common/getItem/visit`, //获取单条数据
    modifyUrl: `${window.base_url}/common/modifyItem/visit`, //修改单条数据
    deleteUrl: `${window.base_url}/common/deleteItem/visit`, //删除单条数据
    searchConfig:[
        {id:'2', title:'输入框类型', name:'jz_id', isAutocomplete:false, ph:'请输入卷宗ID', isMust:false, type:'text', value:{jz_id: {value:'',operator:"="}}},
        {id:'5', title:'单选框类型', name:'planType', isAutocomplete:false, ph:'请选择', isMust:false, type:'radio', value:{planType: {value:'',operator:"="}}, option:[
            {label:'维持原判',value:'维持原判'},
            {label:'申请解冻',value:'申请解冻'},
            {label:'申请解封',value:'申请解封'}
        ]},
        // {id:'5', title:'多选框类型', name:'offend_type', isAutocomplete:false, ph:'请选择', isMust:false, type:'cascader', value:{
        //     offend_type2: {value:'',operator:'='},
        //     offend_type3: {value:'',operator:'='},
        // }, options:[
        //     {
        //         value:'黄赌毒',
        //         label:'黄赌毒',
        //         children: [
        //             {value:'吸毒贩毒', label:'吸毒贩毒'},
        //             {value:'网络赌博', label:'网络赌博'},
        //         ]
        //     },
        //     {
        //         value:'出售违禁物品',
        //         label:'出售违禁物品',
        //         children: [
        //             {value:'网上售烟', label:'网上售烟'},
        //             {value:'出售其他违禁物品', label:'出售其他违禁物品'}
        //         ]
        //     },
        // ]},
        {id:'1', title:'最后修改人', name:'update_user', isAutocomplete:true, ph:'请选择', isMust:false, type:'radio', value:{update_user:{value:'',operator:"="}}},
        {id:'4', title:'最后修改时间', name:'create_time', isAutocomplete:false, ph:'选择日期范围', isMust:false, type:'daterange', value:{create_time: {value:{sdate:"",edate:""},operator:'range'}}}
    ],
    tableConfig:[
        {id:'3', title:'来访用户姓名', name:'visitname', width:120},
        {id:'3', title:'问题帐号', name:'account', width:150},
        {id:'5', title:'最后修改人', name:'update_user', width:120},
        {id:'6', title:'最后修改时间', name:'update_time', width:120},
    ],
    modifyConfig:{
        categorys:['上门基础信息', '处理过程信息', '导流公众号信息', '某某信息', '结论信息'],
        elemts:[
            {id:'1', title:'来访用户手机', name:'mobile', ph:'', isMust:false, type:'text', checkType:'mobile', category:'上门基础信息', width:8, value:''},
            {id:'2', title:'用户问题账号', name:'account', ph:'', isMust:true, type:'multiinput', category:'上门基础信息', width:17, value:[
                {value:''}// 默认写一个
            ]},
            {id:'3', title:'性别', name:'sex', ph:'', isMust:false, type:'selectradio', category:'上门基础信息', width:8, value:'', options:[
                {label:'男', value:'男'},
                {label:'女', value:'女'}
            ]},
            {id:'4', title:'证件号码', name:'idcard', ph:'', isMust:false, type:'text', checkType:'idcard', category:'上门基础信息', width:8, value:''},
            // {id:'5', title:'开店时间', name:'reg_time', ph:'', isMust:false, type:'date',  category:'上门基础信息', width:8, value:''},

            {id:'5', title:'工单类型', name:'orderType', ph:'', isMust:false, type:'selectradio', category:'处理过程信息', width:8, value:'', options:[
                {label:'咨询', value:'咨询'},
                {label:'投诉问题', value:'投诉问题'},
                {label:'寻求合作', value:'寻求合作'},
                {label:'司法流程', value:'司法流程'}
            ]},
            {id:'6', title:'相关产品', name:'product', ph:'', isMust:false, type:'cascader', category:'处理过程信息', width:12, value:{
                bg_id:'',
                service_id:'', 
                service_zh:''
            }},
            {id:'7', title:'问题归档', name:'archive', ph:'', isMust:false, type:'cascader', category:'处理过程信息', width:12, value:{
                archive_path:'', 
                first_dir_code:'',
                second_dir_code:''
            }},
            {id:'8', title:'问题描述', name:'description', ph:'', isMust:false, type:'textarea', category:'处理过程信息', width:16, value:''},

            // {id:'2', title:'关联订单', name:'relative_order', ph:'', isMust:false, type:'inputSelect', dataType:'inputSelect', category:'结论信息', width:17, value:[
            //     {order_num:'', order_way:''}// 默认写一个
            // ], options:[
            //     {label:'扫码支付', value:'扫码支付'},
            //     {label:'刷卡支付', value:'刷卡支付'},
            //     {label:'APP支付', value:'APP支付'},
            //     {label:'公众号支付', value:'公众号支付'},
            //     {label:'wap支付', value:'wap支付'},
            //     {label:'nweb支付', value:'nweb支付'},
            //     {label:'其他', value:'其他'}
            // ]},
            {id:'9', title:'是否产品问题', name:'productProblem', ph:'', isMust:false, type:'selectradio', category:'结论信息', width:8, value:'', options:[
                {label:'是', value:'是'},
                {label:'否', value:'否'}
            ]},
            {id:'10', title:'备注', name:'remark', ph:'', isMust:false, type:'textarea', category:'结论信息', width:24, value:''},
            {id:'11', title:'编辑器', name:'editor', ph:'', isMust:false, type:'editor', category:'结论信息', width:24, value:[]},
            {id:'12', title:'证据截图', name:'proof_photo', ph:'', isMust:false, type:'upload', category:'结论信息', width:24, value:[], uploadedData:[]},
            {id:'13', title:'证据截图1', name:'proof_photo1', ph:'', isMust:false, type:'upload', category:'结论信息', width:24, value:[], uploadedData:[]},
            

            {id:'1', title:'公众号列表', name:'gzhlist', title:'公众号', category:'导流公众号信息', type:'table', span:24, value:[], tableColumns:[
                {id:'1', title:'公众号uin', name:'gzh_username', width:220},
                {id:'1', title:'公众号uin', name:'gzh_username', width:220},
                {id:'2', title:'账号名称', name:'gzh_account', width:200},
                {id:'3', title:'主体名称', name:'gzh_organization', width:200},
                {id:'4', title:'一致性', name:'gzh_match', width:200},
            ], dialogElemts:[
                {id:'5', title:'id', name:'id', ph:'', isMust:false, type:'text', width:6, noRender:true, value:''},
                {id:'1', title:'公众号uin', name:'gzh_username', ph:'', isMust:true, type:'text', width:6, value:''},
                {id:'2', title:'账号名称', name:'gzh_account', ph:'', isMust:false, type:'text', width:6, value:''},
                {id:'3', title:'主体名称', name:'gzh_organization', ph:'', isMust:false, type:'text', width:6, value:''},
                {id:'3', title:'主体名称', name:'gzh_organization', ph:'', isMust:false, type:'text', width:6, value:''},
                {id:'3', title:'主体名称', name:'gzh_organization', ph:'', isMust:false, type:'text', width:6, value:''},
                {id:'1', title:'一致性', name:'gzh_match', ph:'', isMust:false, type:'selectradio', width:6, value:'', options:[
                    {label:'与商户号主体一致', value:'与商户号主体一致'},
                    {label:'与商户号主体不一致', value:'与商户号主体不一致'}
                ]}
            ]},
            {id:'2', title:'某某列表', name:'moumoulist', title:'某某', category:'某某信息', type:'table', span:24, value:[], tableColumns:[
                {id:'1', title:'公众号1', name:'gzh_username1', width:220},
                {id:'2', title:'账号名称1', name:'gzh_account1', width:200},
                {id:'3', title:'主体名称1', name:'gzh_organization1', width:200},
                {id:'4', title:'一致性1', name:'gzh_match1', width:200},
            ], dialogElemts:[
                {id:'1', title:'id', name:'id', ph:'', isMust:false, type:'text', width:6, noRender:true, value:''},
                {id:'1', title:'公众号uin1', name:'gzh_username1', ph:'', isMust:true, type:'text', width:6, value:''},
                {id:'2', title:'账号名称1', name:'gzh_account1', ph:'', isMust:false, type:'text', width:6, value:''},
                {id:'3', title:'主体名称1', name:'gzh_organization1', ph:'', isMust:false, type:'text', width:6, value:''},
                {id:'4', title:'一致性1', name:'gzh_match1', ph:'', isMust:false, type:'selectradio', width:6, value:'', options:[
                    {label:'与商户号主体一致', value:'与商户号主体一致'},
                    {label:'与商户号主体不一致', value:'与商户号主体不一致'}
                ]}
            ]}
        ]
    }
}