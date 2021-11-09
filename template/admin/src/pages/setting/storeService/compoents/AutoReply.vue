<template>

        <Modal :z-index="10" v-model="modals" footer-hide scrollable closable title="自动回复" width="900">
            <Card :bordered="false" dis-hover>
                <Row type="flex" class="mb20">
                    <Col span="24">
                        <Button type="primary" icon="md-add" @click="add" class="mr10">添加自动回复</Button>
                    </Col>
                </Row>
                <Table :columns="columns" :data="tableList" :loading="loading" highlight-row no-userFrom-text="暂无数据" no-filtered-userFrom-text="暂无筛选结果">
                    <template slot-scope="{ row, index }" slot="action">
                        <a @click="edit(row)">编辑</a>
                        <Divider type="vertical" />
                        <a @click="del(row,index)">删除</a>
                    </template>
                </Table>
                <div class="acea-row row-right page">
                    <Page :total="total" show-elevator show-total @on-change="pageChange" :page-size="formValidate.limit" />
                </div>
            </Card>
        </Modal>

</template>

<script>

    import {getAuthReply,saveAuthReply,getAuthReplyForm,deleteAuthReply} from '@/api/user'
    export default {
        name: "AutoReply",
        data(){
            return {
                total:0,
                formValidate:{
                    limit:20,
                    page:1
                },
                modals:false,
                columns: [
                    {
                        title: '关键字',
                        key: 'keyword',
                        width: 200
                    },
                    {
                        title: '回复内容',
                        key: 'content'
                    },
                    {
                        title: '排序',
                        key: 'sort'
                    },
                    {
                        title: '操作',
                        slot: 'action'
                    }
                ],
                tableList:[],
                loading:true
            }
        },
        props:{
            userId:{
                type:Number,
                default:0
            },
            appId:{
                type:String,
                default: ''
            }
        },
        watch:{
            userId(n){
                this.formValidate.page = 1
                this.tableList = []
                this.total = 0
                this.getAuthlist()
            },
        },
        mounted() {
            this.getAuthlist()
        },
        methods:{
            open(){
                this.modals =true
            },
            clear(){
                this.modals = false
            },
            pageChange(index) {
                this.formValidate.page = index
                this.getAuthlist()
            },
            add(){
                this.$modalForm(getAuthReplyForm(0,{user_id:this.userId,appid:this.appId})).then(() => this.getAuthlist())
            },
            edit(row){
                this.$modalForm(getAuthReplyForm(row.id,{user_id:this.userId,appid:this.appId})).then(() => this.getAuthlist())
            },
            del(row,index){
                let delfromData = {
                    title: '删除自动回复',
                    num: index,
                    url: `chat/reply/${row.id}`,
                    method: 'DELETE',
                    ids: '' // 此参数为传递给后端得数据，若无可传空
                }
                this.$modalSure(delfromData).then((res) => {
                    this.$Message.success(res.msg)
                    this.tableList.splice(index, 1)
                }).catch(res => {
                    this.$Message.error(res.msg)
                })
            },
            getAuthlist(){
                getAuthReply({
                    page:this.formValidate.page,
                    limit:this.formValidate.limit,
                    user_id:this.userId,
                    appid:this.appId
                }).then(res=>{
                    this.loading = false
                    this.tableList = res.data.list
                    this.total = res.data.count
                }).catch(()=>{
                    this.loading = false
                })
            }
        }
    }
</script>

<style scoped>

</style>
