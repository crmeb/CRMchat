<template>
  <div>

    <div class="i-layout-page-header">
      <div class="i-layout-page-header">
        <span class="ivu-page-header-title">{{$route.meta.title}}</span>
      </div>
    </div>

    <Card :bordered="false" dis-hover class="ivu-mt">

      <Row type="flex" class="mb20">
        <Col span="24">
        <Button v-auth="['setting-store_service-add']" type="primary" icon="md-add" @click="add" class="mr10">添加客服</Button>
        </Col>
      </Row>

      <Table :columns="columns1" :data="tableList" :loading="loading" highlight-row no-userFrom-text="暂无数据" no-filtered-userFrom-text="暂无筛选结果">
        <template slot-scope="{ row, index }" slot="avatar">
          <div class="tabBox_img" v-viewer>
            <img v-lazy="row.avatar">
          </div>
        </template>
        <template slot-scope="{ row, index }" slot="status">
          <i-switch v-model="row.status" :value="row.status" :true-value="1" :false-value="0" @on-change="onchangeIsShow(row)" size="large">
            <span slot="open">开启</span>
            <span slot="close">关闭</span>
          </i-switch>
        </template>
        <template slot-scope="{ row, index }" slot="online">
          <Tag color="success" v-if="row.online">在线</Tag>
          <Tag color="default" v-else>下线</Tag>
        </template>

        <template slot-scope="{ row, index }" slot="action">
          <a @click="edit(row)">编辑</a>
          <Divider type="vertical" />
          <a @click="del(row,'删除客服',index)">删除</a>
          <Divider type="vertical" v-if="row.status" />
          <a @click="goChat(row)" v-if="row.status">进入工作台</a>
          <Divider type="vertical" />
          <a @click="auth(row)">自动回复</a>
        </template>
      </Table>

      <div class="acea-row row-right page">
        <Page :total="total" show-elevator show-total @on-change="pageChange" :page-size="tableFrom.limit" />
      </div>
    </Card>

    <!--聊天记录-->
    <Modal v-model="modals3" footer-hide scrollable closable title="聊天记录" width="700">
      <div v-if="isChat" class="modelBox">
        <Table :loading="loading3" highlight-row no-userFrom-text="暂无数据" no-filtered-userFrom-text="暂无筛选结果" :columns="columns3" :data="tableList3">
          <template slot-scope="{ row, index }" slot="headimgurl">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="row.headimgurl">
            </div>
          </template>
          <template slot-scope="{ row, index }" slot="action">
            <a @click="look(row)">查看对话</a>
          </template>
        </Table>
        <div class="acea-row row-right page">
          <Page :total="total3" show-elevator show-total @on-change="pageChange3" :page-size="formValidate3.limit" />
        </div>
      </div>
      <div v-if="!isChat">
        <Button type="primary" @click="isChat=true">返回聊天记录</Button>
        <Table :loading="loading5" highlight-row no-userFrom-text="暂无数据" class="mt20" no-filtered-userFrom-text="暂无筛选结果" :columns="columns5" :data="tableList5">
          <template slot-scope="{ row, index }" slot="avatar">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="row.avatar">
            </div>
          </template>
          <template slot-scope="{ row, index }" slot="action">
            <a @click="look(row)">查看对话</a>
          </template>
        </Table>
        <div class="acea-row row-right page">
          <Page :total="total5" show-elevator show-total @on-change="pageChange5" :page-size="formValidate5.limit" />
        </div>
      </div>
    </Modal>
    <auto-reply ref="AutoReply" :userId="userId" :appId="appId"></auto-reply>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import { setCookies } from '@/libs/util'
import {
  kefuListApi, kefucreateApi, kefuaddApi, kefuAddApi,
  kefusetStatusApi, kefuEditApi, kefuRecordApi, kefuChatlistApi, kefuLogin
} from '@/api/setting'
import AutoReply from "./compoents/AutoReply";
export default {
  name: 'index',
  filters: {
    typeFilter(status) {
      const statusMap = {
        'wechat': '微信用户',
        'routine': '小程序用户'
      }
      return statusMap[status]
    }
  },
  components:{
    AutoReply
  },
  computed: {
    ...mapState('media', [
      'isMobile'
    ]),
    ...mapState('userLevel', [
      'categoryId'
    ]),
    labelWidth() {
      return this.isMobile ? undefined : 80
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'left'
    }
  },
  data() {
    return {
      userId:0,
      appId:'',
      isChat: true,
      formValidate3: {
        page: 1,
        limit: 15
      },
      total3: 0,
      loading3: false,
      modals3: false,
      tableList3: [],
      columns3: [
        {
          title: '用户名称',
          key: 'nickname',
          width: 200
        },
        {
          title: '客服头像',
          slot: 'headimgurl'
        },
        {
          title: '操作',
          slot: 'action'
        }
      ],
      formValidate5: {
        page: 1,
        limit: 15,
        uid: 0,
        to_uid: 0,
        id: 0
      },
      total5: 0,
      loading5: false,
      tableList5: [],
      columns5: [
        {
          title: '用户名称',
          key: 'nickname',
          width: 200
        },
        {
          title: '用户头像',
          slot: 'avatar'
        },
        {
          title: '发送消息',
          key: 'msn',
          width: 250
        },
        {
          title: '发送时间',
          key: 'add_time'
        }
      ],
      FromData: null,
      formValidate: {
        page: 1,
        limit: 15,
        data: '',
        type: '',
        nickname: ''
      },
      tableList2: [],
      modals: false,
      total: 0,
      tableFrom: {
        page: 1,
        limit: 15
      },
      timeVal: [],
      fromList: {
        title: '选择时间',
        custom: true,
        fromTxt: [
          { text: '全部', val: '' },
          { text: '今天', val: 'today' },
          { text: '昨天', val: 'yesterday' },
          { text: '最近7天', val: 'lately7' },
          { text: '最近30天', val: 'lately30' },
          { text: '本月', val: 'month' },
          { text: '本年', val: 'year' }
        ]
      },
      loading: false,
      tableList: [],
      columns1: [
        {
          title: 'ID',
          key: 'id',
          width: 80
        },
        {
          title: '客服名称',
          key: 'nickname',
          minWidth: 60
        },
        {
          title: '客服账号',
          key: 'account',
          minWidth: 60
        },
        {
          title: '客服状态',
          slot: 'status',
          minWidth: 60
        },
        {
          title: '是否上线',
          slot: 'online',
          minWidth: 120
        },
        {
          title: '添加时间',
          key: 'add_time',
          minWidth: 120
        },
        {
          title: '操作',
          slot: 'action',
          fixed: 'right',
          minWidth: 150
        }
      ],
      columns4: [
        {
          type: 'selection',
          width: 60,
          align: 'center'
        },
        {
          title: 'ID',
          key: 'uid',
          width: 80
        },
        {
          title: '微信用户名称',
          key: 'nickname',
          minWidth: 160
        },
        {
          title: '客服头像',
          slot: 'headimgurl',
          minWidth: 60
        },
        {
          title: '用户类型',
          slot: 'user_type',
          width: 100
        },
        {
          title: '性别',
          slot: 'sex',
          minWidth: 60
        },
        {
          title: '地区',
          slot: 'country',
          minWidth: 120
        },
        {
          title: '是否关注公众号',
          slot: 'subscribe',
          minWidth: 120
        }
      ],
      loading2: false,
      total2: 0,
      addFrom: {
        uids: []
      },
      selections: [],
      rows: {},
      rowRecord: {}
    }
  },
  created() {
    this.getList()
  },
  methods: {
    auth(item){
      this.userId = item.user_id
      this.appId = item.appid
      this.$refs.AutoReply.open()
    },
    // 进入工作台
    goChat(item) {
      kefuLogin(item.id).then(res => {
        var url = ''
        if(res.data.token) {
          let expires = this.getExpiresTime(res.data.exp_time);
          setCookies('kefu_token', res.data.token, expires);
          setCookies('kefu_uuid', res.data.kefuInfo.uid, expires);
          setCookies('kefu_expires_time', res.data.exp_time, expires);
          setCookies('kefuInfo', res.data.kefuInfo, expires);
          console.log(this.$store.state.media.isMobile, 'this.$store.state.media.isMobile');

          if(this.$store.state.media.isMobile) {
            url = window.location.protocol + "//" + window.location.host + '/kefu/mobile_list';
          } else {
            url = window.location.protocol + "//" + window.location.host + '/kefu/pc_list';
          }


          window.open(url, '_blank');
        }
      }).catch(error => {
        this.$Message.error(error.msg)
      })
    },
    getExpiresTime(expiresTime) {
      let nowTimeNum = Math.round(new Date() / 1000);
      let expiresTimeNum = expiresTime - nowTimeNum;
      return parseFloat(parseFloat(parseFloat(expiresTimeNum / 60) / 60) / 24);
    },
    cancel() {
      this.formValidate = {
        page: 1,
        limit: 10,
        data: '',
        type: '',
        nickname: ''
      }
    },
    handleReachBottom() {
      return new Promise(resolve => {
        this.formValidate.page = this.formValidate.page + 1
        setTimeout(() => {
          // this.loading2 = true;
          kefucreateApi(this.formValidate).then(async res => {
            let data = res.data
            // this.tableList2 = data.list;
            if(data.list.length > 0) {
              for(let i = 0; i < data.list.length; i++) {
                this.tableList2.push(data.list[i])
              }
            }
            this.total2 = data.count
            this.loading2 = false
          }).catch(res => {
            this.loading2 = false
            this.$Message.error(res.msg)
          })
          resolve()
        }, 2000)
      })
    },
    // 查看对话
    look(row) {
      this.isChat = false
      this.rowRecord = row
      this.getChatlist()
    },
    // 查看对话列表
    getChatlist() {
      this.loading5 = true
      this.formValidate5.uid = this.rows.uid
      this.formValidate5.to_uid = this.rowRecord.uid
      this.formValidate5.id = this.rows.id
      kefuChatlistApi(this.formValidate5).then(async res => {
        let data = res.data
        this.tableList5 = data.list
        this.total5 = data.count
        this.loading5 = false
      }).catch(res => {
        this.loading5 = false
        this.$Message.error(res.msg)
      })
    },
    pageChange5(index) {
      this.formValidate5.page = index
      this.getChatlist()
    },
    // 修改成功
    submitFail() {
      this.getList()
    },
    // 聊天记录
    record(row) {
      this.rows = row
      this.modals3 = true
      this.isChat = true
      this.getListRecord()
    },
    // 聊天记录列表
    getListRecord() {
      this.loading3 = true
      kefuRecordApi(this.formValidate3, this.rows.id).then(async res => {
        let data = res.data
        this.tableList3 = data.list ? data.list : []
        this.total3 = data.count
        this.loading3 = false
      }).catch(res => {
        this.loading3 = false
        this.$Message.error(res.msg)
      })
    },
    pageChange3(index) {
      this.formValidate3.page = index
      this.getListRecord()
    },
    // 编辑
    edit(row) {
      this.$modalForm(kefuEditApi(row.id)).then(() => this.getList())
    },
    // 添加
    add() {
      // this.modals = true;
      // this.formValidate.data = '';
      // this.getListService();
      this.$modalForm(kefuaddApi()).then(() => {
        this.getList();
        console.log(1223);
      })
    },
    // 全选
    onSelectTab(selection) {
      this.selections = selection
      let data = []
      this.selections.map((item) => {
        data.push(item.uid)
      })
      this.addFrom.uids = data
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e
      this.formValidate.data = this.timeVal.join('-')
      this.formValidate.page = 1
      this.getListService()
    },
    // 选择时间
    selectChange(tab) {
      this.formValidate.data = tab
      this.timeVal = []
      this.formValidate.page = 1
      this.getListService()
    },
    // 客服列表
    getListService() {
      this.loading2 = true
      kefucreateApi(this.formValidate).then(async res => {
        let data = res.data
        this.tableList2 = data.list
        this.total2 = data.count
        this.tableList2.map((item) => {
          item._isChecked = false
        })
        this.loading2 = false
      }).catch(res => {
        this.loading2 = false
        this.$Message.error(res.msg)
      })
    },
    pageChange2(pageIndex) {
      this.formValidate.page = pageIndex
      this.getListService()
      this.addFrom.uids = []
    },
    // 搜索
    userSearchs() {
      this.formValidate.page = 1
      this.getListService()
    },
    // 删除客服
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `chat/kefu/${row.id}`,
        method: 'DELETE',
        ids: '' // 此参数为传递给后端得数据，若无可传空
      }
      this.$modalSure(delfromData).then((res) => {
        this.$Message.success(res.msg)
        this.tableList.splice(num, 1)
      }).catch(res => {
        this.$Message.error(res.msg)
      })
    },
    // 列表
    getList() {
      this.loading = true
      kefuListApi(this.tableFrom).then(async res => {
        let data = res.data
        this.tableList = data.list
        this.total = res.data.count
        this.loading = false
      }).catch(res => {
        this.loading = false
        this.$Message.error(res.msg)
      })
    },
    pageChange(index) {
      this.tableFrom.page = index
      this.getList()
    },
    // 修改是否显示
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        status: row.status
      }
      kefusetStatusApi(data).then(async res => {
        this.$Message.success(res.msg);
        this.getList();
      }).catch(res => {
        this.$Message.error(res.msg)
      })
    },
    // 添加客服
    putRemark() {
      if(this.addFrom.uids.length === 0) {
        return this.$Message.warning('请选择要添加的客服')
      }
      kefuAddApi(this.addFrom).then(async res => {
        this.$Message.success(res.msg)
        this.modals = false
        this.getList()
      }).catch(res => {
        this.loading = false
        this.$Message.error(res.msg)
      })
    }
  }
}
</script>

<style scoped lang="less">
.tabBox_img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}

.modelBox {
  /deep/ .ivu-table-header {
    width: 100% !important;
  }
}

.trees-coadd {
  width: 100%;
  height: 385px;

  .scollhide {
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: scroll;
  }
}

// margin-left: 18px;
.scollhide::-webkit-scrollbar {
  display: none;
}
</style>
