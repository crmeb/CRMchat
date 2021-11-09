<template>
    <div class="msg-box">
        <div class="head">
            <div class="search-box">
                <div class="caption">
                    <div>自动回复开关:</div>
                    <div class="switch">
                        <i-switch v-model="authReply" @on-change="changeSwitch" ></i-switch>
                    </div>
                </div>
            </div>

        </div>
        <div class="main">
            <div class="right-box">

                <Scroll :on-reach-bottom="handleReachBottom" class="right-scroll" height="360">
                    <div class="msg-item add-box" v-if="tabCur" style="margin-top: 0">
                        <div class="box2">
                            <Input class="input-box" v-model="addMsg.keyword" placeholder="输入关键字,多个关键字用逗号隔开" style="width: 100%" @on-focus="bindFocus" />
                            <div class="conBox" :class="{active:addMsg.isEdit}">
                                <div class="content">
                                    <Input v-model="addMsg.content" type="textarea" :rows="4" placeholder="请输入内容" />
                                </div>
                                <div class="bom">
                                    <div class="select">
                                        <Input v-model="addMsg.sort" type="number" placeholder="请输入排序" />
                                    </div>
                                    <div class="btns-box">
                                        <Button @click.stop="addMsg.isEdit = false">取消</Button>
                                        <Button type="primary" @click.stop="bindAdd">保存</Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="msg-item" v-for="(item,index) in list" :key="index" v-if="item.id">
                        <div class="box1" v-if="!item.isEdit">
                            <div class="txt-box" @click="bindRadio(item)">
                                <span class="title" v-if="item.keyword">{{item.keyword | filtersTitle}}</span>
                                <span v-if="item.content">{{item.content | filtersCon}}</span>

                            </div>
                            <div class="edit-box" v-if="tabCur">
                                <span class="iconfont iconbianji" @click.stop="editMsg(item)"></span>
                                <span class="iconfont iconshanchu" @click.stop="delMsg(item,'删除话术',index)"></span>
                            </div>
                        </div>
                        <div class="box2" v-else>
                            <Input class="input-box" v-model="item.keyword" placeholder="输入关键字,多个关键字用逗号隔开" style="width: 100%" />
                            <div class="content">
                                <Input v-model="item.content" type="textarea" :rows="4" placeholder="请输入内容" />
                            </div>
                            <div class="bom">
                                <div class="select">
                                    <Input v-model="item.sort" type="number" placeholder="请输入排序" />
                                </div>
                                <div class="btns-box">
                                    <Button @click.stop="item.isEdit = false">取消</Button>
                                    <Button type="primary" @click.stop="updataMsg(item)">保存</Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </Scroll>

            </div>
        </div>
    </div>
</template>

<script>
    import { saveAuthReply , getAuthReply,updateAuthReply,getKefuInfo } from '@/api/kefu'
    export default {
        name: "msgWindow",
        data() {
            return {
                ops: {
                    vuescroll: {
                        mode: "native",
                        enable: false,
                        tips: {
                            deactive: 'Push to Load',
                            active: 'Release to Load',
                            start: 'Loading...',
                            beforeDeactive: 'Load Successfully!'
                        },
                        auto: false,
                        autoLoadDistance: 0,
                        pullRefresh: {
                            enable: false
                        },
                        pushLoad: {
                            enable: false,
                            auto: true,
                            autoLoadDistance: 10
                        }
                    },
                    bar: {
                        background: '#393232',
                        opacity: '.5',
                        size: '2px'
                    }
                },
                authReply:false,
                isScroll: true,
                page: 1,
                limit: 10,
                tabCur: 1,
                searchTxt: '', // 搜索
                list: [
                    {
                        isEdit: false
                    }
                ], // 列表
                model1: '',
                msgTitle: '', // 填写的标题
                sortList: [], // 分类
                addMsg: {
                    keyword: '',
                    content: '',
                    sort: 0,
                    isEdit: false
                },
                isAddSort: false, // 添加分类
                classTitle: '', // 分类名称
                classSort: '', // 分类排序
                maskTitle: '', // 弹窗标题
                editObj: {} // 编辑分类对象
            }
        },
        filters: {
            filtersTitle(val) {
                let len = 37
                if(val.length > len) {
                    let data = val.substring(0, len)
                    return `${data}...`;
                } else {
                    return val
                }
            },
            filtersCon(val) {
                let len = 113
                if(val.length > len) {
                    let data = val.substring(0, len)
                    return `${data}...`;
                } else {
                    return val
                }
            }
        },
        mounted() {
            let self = this
            this.getKefuInfo()
            this.getList()
        },
        methods: {
            getKefuInfo(){
                getKefuInfo().then(res=>{
                    this.authReply = !!res.data.auto_reply
                })
            },
            changeSwitch(status){
                updateAuthReply(status ? 1:0).then(res=>{
                    this.$Message.success('设置成功')
                }).catch(res=>{
                    this.$Message.error(res.msg)
                })
            },
            // 打开编辑
            editMsg(item) {
                item.isEdit = true
            },
            // 编辑框
            bindEdit(item, index) {
                if(index == 0) {
                    return
                } else {
                    item.isEdit = !item.isEdit
                }
            },
            // 搜索
            bindSearch() {
                this.isScroll = true
                this.page = 1
                this.list = []
                this.getList()
            },
            // 获取列表
            getList() {
                if(!this.isScroll) return
                getAuthReply({
                    page: this.page,
                    limit: this.limit,
                }).then(res => {
                    this.isScroll = res.data.length >= this.limit
                    res.data.forEach((el, index) => {
                        el.isEdit = false
                    })
                    this.page++
                    this.list = this.list.concat(res.data)
                })
            },
            // 修改话术
            updataMsg(item) {
                saveAuthReply(item.id, {
                    keyword: item.keyword,
                    content: item.content,
                    sort: item.sort
                }).then(res => {
                    this.$Message.success('修改成功')
                    item.isEdit = false
                }).catch(error => {
                    this.$Message.error(error.msg)
                    item.isEdit = true
                })
            },
            // 添加框显示
            bindFocus() {
                this.list.forEach((el, item) => {
                    el.isEdit = false
                })
                this.addMsg.cateId = this.cateId;
                this.addMsg.isEdit = true
            },
            // 添加话术
            bindAdd() {
                saveAuthReply(0,{
                    keyword: this.addMsg.keyword,
                    content: this.addMsg.content,
                    sort: this.addMsg.sort
                }).then(res => {
                    this.addMsg.keyword = ''
                    this.addMsg.content = ''
                    this.addMsg.sort = 0
                    this.addMsg.isEdit = false
                    this.$Message.success(res.msg)
                    res.data.isEdit = false
                    this.page = 1
                    this.list = []
                    this.isScroll = true
                    this.getList();

                }).catch(error => {
                    this.$Message.error(error.msg)
                })
            },
            // 删除
            delMsg(row, tit, num, type) {
                let delfromData = {
                    title: tit,
                    num: num,
                    url: `service/auth_reply/${row.id}`,
                    method: 'DELETE',
                    ids: '',
                    kefu: true
                };
                this.$modalSure(delfromData).then((res) => {
                    this.$Message.success(res.msg);
                    this.list.splice(num, 1)
                }).catch(res => {
                    this.$Message.error(res.msg);
                });
            },
            handleReachBottom() {
                this.getList()
            },
            bindRadio(data) {
                this.$emit('activeTxt', data.message)
            }
        }
    }
</script>

<style lang="stylus" scoped>
    .head {
        padding: 15px 14px 0;

    .tab-bar {
        display: flex;

    .tab-item {
        margin-right: 24px;
        color: #999;
        font-size: 14px;
        font-weight: 500;

    &.on {
         color: #333333;
     }
    }
    }

    .search-box {
        margin-top: 15px;
    }
    }

    .main {
        display: flex;
        margin-top: 15px;
        height: 365px;

    .left-box {
        width: 106px;
        height: 100%;
        border-right: 1px solid #ECECEC;
        overflow: hidden;

    .left-item {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 36px;
        padding: 0 10px 0 14px;
        font-size: 13px;
        cursor: pointer;

    &.on {
         background: #F0FAFE;
         color: #1890FF;
         border-right: 2px solid #1890FF;

    .iconDot {
        z-index: 1;
        opacity: 1;
    }
    }

    &:nth-child(1).on, &:nth-child(2).on {
    .iconDot {
        display: none;
    }
    }

    .iconaddto {
        font-size: 12px;
    }

    .iconDot {
        z-index: -1;
        opacity: 0;
    }

    .edit-wrapper {
        z-index: 50;
        position: absolute;
        right: -2px;
        top: -4px;
        background: #fff;
        width: 80px;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.2);
        border-radius: 4px;

    .edit-item {
        padding: 8px 16px;
        color: #666 !important;
        cursor: pointer;
    }
    }

    .edit-bg {
        z-index: 40;
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: transparent;
    }
    }
    }

    .right-box {
        flex: 1;
        padding: 0 12px;
        overflow-x: hidden;

    .msg-item {
        margin-top: 12px;
        transition: all 0.3s ease;
        cursor: pointer;

    .box1 {
        position: relative;
        display: flex;

    .txt-box {
        flex: 1;
        font-size: 12px;
        color: #999999;

    .title {
        max-width: 370px;
        margin-right: 5px;
        color: #333;
        font-weight: 700;
    }
    }

    .edit-box {
        z-index: -1;
        opacity: 0;
        position: absolute;
        right: 7px;
        top: 0;
        width: 60px;
        height: 30px;
        background: #fff;

    .iconfont {
        margin: 0 8px;
        color: #000000;
        font-size: 16px;
        cursor: pointer;
    }
    }
    }

    .box2 {
        padding-bottom: 15px;
        border-radius: 5px;
        background: #F5F5F5;

    .input-box {
        border-bottom: 1px solid #EEEEEE;

    >>> .ivu-input {
        background: transparent;
        border: 0;
        border-radius: 0;
    }
    }

    .content {
        font-size: 12px;
        padding: 12px 11px 0;
        color: #333333;
    }

    .bom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px 0 11px;
        margin-top: 10px;

    button {
        margin-left: 8px;
        width: 70px;
    }
    }
    }

    &:hover {
         transition: all 0.3s ease;

    .box1 .edit-box {
        z-index: 1;
        opacity: 1;
        transition: all 0.3s ease;
    }
    }
    }

    .add-box {
        border-radius: 0;
        margin-bottom: 10px;

    .box2 {
        padding-bottom: 0;
        border-radius: 0;

    .conBox {
        height: 0;
        overflow: hidden;

    &.active {
         animation: mymove 0.4s ease;
         animation-iteration-count: 1;
         animation-fill-mode: forwards;
     }
    }
    }
    }
    }
    }

    .right-scroll {
    >>> .ivu-scroll-container .ivu-scroll-loader:nth-child(1) {
        display: none;
    }
    }

    .class-box {
    .item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;

    &:last-child {
         margin-bottom: 0;
     }

    input {
        flex: 1;
    }

    span {
        width: 80px;
        font-size: 12px;
    }
    }
    }

    .msg-box .caption{
        background-color: #fff;
        display: flex;
        align-items: center;
        /*justify-content: center;*/
    }
    .msg-box .caption .switch{
        margin-left: 10px;
    }
</style>
<style>
    @keyframes mymove {
        0% {
            height: 0;
        }
        100% {
            height: 150px;
        }
    }
</style>
