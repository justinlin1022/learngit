         <template>
    <el-cascader
        filterable
        :options="items"
        :show-all-levels="false"
        :placeholder="placeholder"
        :change-on-select="!isLast"
        clearable
        v-model="defaultValue"
        @change="handleChange"
        :props="archiveProps" >
    </el-cascader>
</template>

<script>
    import ajax from 'admin/api/ajax';  
    export default {
        data() {
            return {
                items:[],
                keyNames:[],
                defaultValue:[],
                archiveProps: {
                    value: 'id',
                    label: 'name'
                },
            }
        },
        props:{
            value:{
                type:Array
            },
            type:{
                type:String,
                default: 'product'
            },
            productid:{
                type:String,
                default: ''
            },
            placeholder:{
                type:String,
                default:'请选择'
            },
            isLast:{
                type:Boolean,
                default: true
            }
        },
        mounted: function(){
            this.defaultValue = this.value;
            if(this.type == 'product'){
                this.fetchBgProductTree();//获取所有的产品
            }else{
                this.fetchLastArchive(this.productid);//获取产品归档路径
            }
        },
        watch: {
            'value': function(newVal,oldVal){
                this.defaultValue = newVal;
            },
            'productid': function(newVal,oldVal){
                this.fetchLastArchive(this.productid);//获取产品归档路径
            }
        },
        methods: {
            handleChange(val){
                let val_zh = '';// 每个产品id对应的中文名
                for(let i in this.items) {
                    if(this.items[i].value == val[0]) {
                        for(let n in this.items[i].children) {
                            // this.items[i].children[n]就是每个最终选择的对象数据
                            if(this.items[i].children[n].id == val[1]) {
                                val_zh = this.items[i].children[n].label;
                            }
                        }
                    }
                }
                //选中回调父组件
                this.$emit('change',val,val_zh);
            },
            fetchBgProductTree(){
                ajax({
                    url: window.base_url + 'admin/common/getBgProductTree/', 
                    method: 'get',
                    data: {}})
                    .then((response)=>{
                    if(response.body.retcode == 0){
                        this.items = response.body.data;
                    }
                });
            },
            fetchLastArchive(prodcut_id){
                ajax({
                    url: window.base_url + 'admin/common/getLastArchiveByProductId/'+prodcut_id, 
                    method: 'get',
                    data: {}})
                    .then((response)=>{
                    if(response.body.retcode == 0){
                        this.items = response.body.data;
                        this.keyNames = response.body.keyNames;
                    }
                });
            },
        }
    }
</script>