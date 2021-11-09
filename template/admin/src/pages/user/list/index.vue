<template>
  <div>
    <div class="i-layout-page-header">
      <div class="i-layout-page-header">
        <span class="ivu-page-header-title">用户管理</span>
        <div>
          <Tabs @on-click="onClickTab">
            <TabPane :label="item.name" :name="item.type" v-for="(item,index) in headeNum" :key="index" />
          </Tabs>
        </div>
      </div>
    </div>
    <Card :bordered="false" dis-hover class="ivu-mt listbox">
      <Form ref="userFrom" :model="userFrom" :label-width="labelWidth" :label-position="labelPosition" @submit.native.prevent>
        <Row :gutter="16">
          <Col span="18">
          <Col span="24">
          <Col v-bind="grid">
          <FormItem label="用户搜索：" label-for="nickname">
            <Input v-model="userFrom.nickname" placeholder="请输入" element-id="nickname" clearable>
            <Select v-model="field_key" slot="prepend" style="width: 80px">
              <Option value="all">全部</Option>
              <Option value="id">ID</Option>
              <Option value="phone">手机号</Option>
              <Option value="nickname">用户昵称</Option>
            </Select>
            </Input>
          </FormItem>
          </Col>
          </Col>
          </Col>
          <template v-if="collapse">
            <Col span="24">
            <Col v-bind="grid">
            <FormItem label="用户分组：" label-for="group_id">
              <Select v-model="group_id" placeholder="请选择" element-id="group_id" clearable>
                <Option value="all">全部</Option>
                <Option :value="item.id" v-for="(item, index) in groupList" :key="index">{{item.group_name}}</Option>
              </Select>
            </FormItem>
            </Col>
            <Col v-bind="grid">
            <FormItem label="用户标签：" label-for="label_id">
              <Select multiple @on-change="changeLabel" v-model="label_id" placeholder="请选择" element-id="label_id" clearable>
                <OptionGroup :label="item.label" v-for="(item,index) in selectLabel" :key="index">
                  <Option :value="v.id" v-for="(v,k) in item.options" :key="k">{{v.label}}</Option>
                </OptionGroup>
              </Select>
            </FormItem>
            </Col>
            <Col v-bind="grid">
            <FormItem label="性别：" label-for="sex">
              <RadioGroup v-model="userFrom.sex" type="button">
                <Radio label="">
                  <span>全部</span>
                </Radio>
                <Radio label="1">
                  <span>男</span>
                </Radio>
                <Radio label="2">
                  <span>女</span>
                </Radio>
                <Radio label="0">
                  <span>保密</span>
                </Radio>
              </RadioGroup>
            </FormItem>
            </Col>
            </Col>
            <Col span="18">
                <Col v-bind="grid">
                <FormItem label="选择时间：" label-for="user_time">
                  <DatePicker :editable="false" @on-change="onchangeTime" :value="timeVal" format="yyyy/MM/dd" type="datetimerange" placement="bottom-start" placeholder="自定义时间" style="width: 300px;" class="mr20" :options="options"></DatePicker>
                </FormItem>

                </Col>
                <Col v-bind="grid">
                  <FormItem label="用户来源：" label-for="group_id">
                    <Select v-model="user_type" placeholder="请选择" element-id="user_type" clearable>
                      <Option value="all">全部</Option>
                      <Option :value="item.value" v-for="(item, index) in selectType" :key="index">{{item.label}}</Option>
                    </Select>
                  </FormItem>
                </Col>
              </Col>

          </template>
          <Col span="6" class="ivu-text-right userFrom">
          <FormItem>
            <Button type="primary" icon="ios-search" label="default" class="mr15" @click="userSearchs">搜索</Button>
            <Button class="ResetSearch" @click="reset('userFrom')">重置</Button>
            <a class="ivu-ml-8 font14 ml10" @click="collapse = !collapse">
              <template v-if="!collapse">
                展开
                <Icon type="ios-arrow-down" />
              </template>
              <template v-else>
                收起
                <Icon type="ios-arrow-up" />
              </template>
            </a>
          </FormItem>
          </Col>
        </Row>
      </Form>
      <Divider dashed />
      <Row type="flex" justify="space-between" class="mt20">
        <Col span="24">
        <Button v-auth="['admin-user-group_set']" class="mr20" @click="setGroup">批量设置分组</Button>
        <Button v-auth="['admin-user-set_label']" class="mr20" @click="setLabel">批量设置标签</Button>
        </Col>
        <Col span="24" class="userAlert" v-if="selectionList.length">
        <Alert show-icon> 已选择<i class="userI"> {{selectionList.length}} </i>项</Alert>
        </Col>
      </Row>
      <Table :columns="columns" :data="userLists" class="mt25" ref="table" highlight-row :loading="loading" no-userFrom-text="暂无数据" no-filtered-userFrom-text="暂无筛选结果" @on-selection-change="onSelectTab" @on-sort-change="sortChanged">

        <template slot-scope="{ row, index }" slot="avatars">
          <div class="tabBox_img" v-viewer>
            <img v-lazy="row.avatar">
          </div>
        </template>

        <template slot-scope="{ row, index }" slot="nickname">
          <div class="acea-row">
            <Icon type="md-male" v-show="row.sex==='男'" color="#2db7f5" size="15" class="mr5" />
            <Icon type="md-female" v-show="row.sex==='女'" color="#ed4014" size="15" class="mr5" />
            <div v-text="row.nickname"></div>
          </div>
        </template>

        <template slot-scope="{ row, index }" slot="group_id">
          <div>
            {{groupListObj[row.group_id]}}
          </div>
        </template>

        <template slot-scope="{ row, index }" slot="is_tourist">
          <div>
            {{row.is_tourist ? '游客': '客户'}}
          </div>
        </template>

        <template slot-scope="{ row, index }" slot="label">
          <div v-for="item in row.label">
             <span>{{item.label}}</span>
          </div>
        </template>

        <template slot-scope="{ row, index }" slot="action">
          <a @click="edit(row)">编辑</a>
          <Divider type="vertical" />
          <template>
            <Dropdown @on-click="changeMenu(row,$event,index)">
              <a href="javascript:void(0)">
                更多
                <Icon type="ios-arrow-down"></Icon>
              </a>
              <DropdownMenu slot="list">
                <DropdownItem name="1">设置标签</DropdownItem>
                <DropdownItem name="2">设置分组</DropdownItem>
              </DropdownMenu>
            </Dropdown>
          </template>
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page :total="total" :current="userFrom.page" show-elevator show-total @on-change="pageChange" :page-size="userFrom.limit" />
      </div>
    </Card>
    <!-- 编辑表单 积分余额-->
    <edit-from ref="edits" :FromData="FromData" @submitFail="submitFail"></edit-from>
    <!-- 会员详情-->
    <user-details ref="userDetails"></user-details>
    <!--发送图文消息 -->
    <Modal v-model="modal13" scrollable title="发送消息" width="1200" height="800" footer-hide class="modelBox">
      <news-category v-if="modal13" :isShowSend="isShowSend" :userIds="user_ids" :scrollerHeight="scrollerHeight" :contentTop="contentTop" :contentWidth="contentWidth" :maxCols="maxCols"></news-category>
    </Modal>

    <Modal v-model="labelShow" scrollable title="请选择用户标签" :closable="false" width="320" :footer-hide="true">
      <userLabel v-if="labelShow" :uid="labelActive.uid" @close="labelClose" @onceGetList="userGroup"></userLabel>
    </Modal>

    <Modal v-model="setUserGroupModel" scrollable title="请选择用户分组" :closable="false" width="320" :footer-hide="true">
      <user-group v-if="setUserGroupModel" :activeUserInfo="activeUserInfo" :userGroup="getUserGroup" @handleSelectGroup="handleSelectGroup" @close="setUserGroupModel=false"></user-group>
    </Modal>

    <Modal v-model="labelGroupSelect.model" scrollable title="批量设置用户标签" :closable="false" width="320" :footer-hide="true">
      <label-group v-if="labelGroupSelect.model" :labelList="labelGroupSelect.list" @close="labelGroupSelect.model=false" @confrim="handleEditLabelGroup"></label-group>
    </Modal>

    <Modal v-model="userGroupSelect.model" scrollable title="批量设置用户分组" :closable="false" width="320" :footer-hide="true">
      <user-group v-if="userGroupSelect.model" :userGroup="userGroupSelect.list" @handleSelectGroup="handleselectUsersGroup" @close="userGroupSelect.model=false"></user-group>
    </Modal>

  </div>
</template>

<script>
import userLabel from "../../../components/userLabel";
import userGroup from './components/userGroup'; // 用户分组
import labelGroup from './components/labelGroup';
import { mapState } from 'vuex';
import { userList, getUserData,getUserLabelAllApi, isShowApi, userSetGroup, userGroupApi, userSetLabelApi, userLabelApi, userSynchro, putUserLabel, userBatchGroupApi } from '@/api/user';
import { agentSpreadApi } from '@/api/agent'
import editFrom from '../../../components/from/from';
import sendFrom from '@/components/sendCoupons/index';
import userDetails from './handle/userDetails';
import newsCategory from '@/components/newsCategory/index';
import city from '@/utils/city';
import customerInfo from '@/components/customerInfo'
export default {
  name: 'user_list',
  components: { editFrom, sendFrom, userDetails, newsCategory, customerInfo, userLabel, userGroup, labelGroup },
  data() {
    return {
      setUserGroupModel: false, // 设置用户分组
      getUserGroup: [], // 获取的用户分组
      activeUserInfo: {}, // 用户数据存储容器
      // 选择用户标签变量容器
      labelGroupSelect: {
        model: false, // 弹框
        list: [] // 数组
      },
      userGroupSelect: {
        model: false,
        list: []
      },
      labelShow: false,
      customerShow: false,
      promoterShow: false,
      labelActive: {
        uid: 0
      },
      formInline: {
        uid: 0,
        spread_uid: 0,
        image: ''
      },
      options: {
        shortcuts: [
          {
            text: '今天',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()));
              return [start, end];
            }
          },
          {
            text: '昨天',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 1)));
              end.setTime(end.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 1)));
              return [start, end];
            }
          },
          {
            text: '最近7天',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 6)));
              return [start, end];
            }
          },
          {
            text: '最近30天',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 29)));
              return [start, end];
            }
          },
          {
            text: '本月',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), 1)));
              return [start, end];
            }
          },
          {
            text: '本年',
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(start.setTime(new Date(new Date().getFullYear(), 0, 1)));
              return [start, end];
            }
          }
        ]
      },
      collapse: false,
      headeNum: [
        { 'type': 'all', 'name': '全部' },
        { 'type': 1, 'name': '游客' },
        { 'type': 0, 'name': '用户' },
      ],
      address: [],
      addresData: city,
      isShowSend: true,
      modal13: false,
      maxCols: 4,
      scrollerHeight: '600',
      contentTop: '130',
      contentWidth: '98%',
      grid: {
        xl: 8,
        lg: 8,
        md: 12,
        sm: 24,
        xs: 24
      },
      grid2: {
        xl: 18,
        lg: 16,
        md: 12,
        sm: 24,
        xs: 24
      },
      loading: false,
      total: 0,
      userFrom: {
        label_id: [],
        user_type: '',
        status: '',
        sex: '',
        time: '',
        nickname: '',
        page: 1,
        limit: 15,
        group_id: '',
        field_key: '',
      },
      field_key: '',
      level: '',
      group_id: '',
      label_id: [],
      user_time_type: '',
      pay_count: '',
      columns: [
        {
          type: 'selection',
          width: 60,
          align: 'center'
        },
        {
          title: 'ID',
          key: 'id',
          width: 80
        },
        {
          title: '头像',
          slot: 'avatars',
          minWidth: 60
        },
        {
          title: '昵称',
          slot: 'nickname',
          minWidth: 150
        },
        {
          title: '备注昵称',
          key: 'remark_nickname',
          minWidth: 150
        },
        {
          title: '用户类型',
          slot: 'is_tourist',
          minWidth: 100
        },
        {
          title: '分组',
          slot: 'group_id',
          minWidth: 100
        },
        {
          title: '手机号',
          key: 'phone',
          minWidth: 100
        },
        {
          title: '用户来源',
          key: 'type',
          minWidth: 100
        },
        {
          title: '用户标签',
          slot: 'label',
          minWidth: 100
        },
        {
          title: '操作',
          slot: 'action',
          fixed: 'right',
          minWidth: 120
        }
      ],
      userLists: [],
      FromData: null,
      selectionList: [],
      user_ids: '',
      selectedData: [],
      timeVal: [],
      array_ids: [],
      groupList: [],
      groupListObj: {},
      levelList: [],
      labelFrom: {
        page: 1,
        limit: ''
      },
      labelLists: [],
      selectLabel:[],
      selectType:[
        {label:'PC',value:0},
        {label:'微信',value:1},
        {label:'小程序',value:2},
        {label:'H5',value:3},
        {label:'APP',value:4},
      ],
      user_type:'all'
    }
  },
  computed: {
    ...mapState('media', [
      'isMobile'
    ]),
    labelWidth() {
      return this.isMobile ? undefined : 100;
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    }
  },
  created() {
    this.getList();
  },
  mounted() {
    this.userGroup();
    this.groupLists();
    this.getUserLabelAll()
  },
  methods: {
    getUserLabelAll(){
      getUserLabelAllApi().then(res=>{
        this.selectLabel = res.data
      })
    },
    onceGetList() {
      this.getList();
    },
    // 标签弹窗关闭
    labelClose() {
      this.labelShow = false
    },
    changeLabel(){
      this.userFrom.page = 1;
      this.getList();
    },
    // 提交
    putSend(name) {
      this.$refs[name].validate((valid) => {
        if(valid) {
          if(!this.formInline.spread_uid) {
            return this.$Message.error('请上传用户');
          }
          agentSpreadApi(this.formInline).then(res => {
            this.promoterShow = false;
            this.$Message.success(res.msg);
            this.getList();
            this.$refs[name].resetFields();
          }).catch(res => {
            this.$Message.error(res.msg);
          })
        }
      })
    },
    synchro() {
      userSynchro().then(res => {
        this.$Message.success(res.msg);
      }).catch(res => {
        this.$Message.error(res.msg);
      })
    },
    // 分组列表
    groupLists() {
      this.loading = true;
      userLabelApi(this.labelFrom).then(async res => {
        let data = res.data;
        this.labelLists = data.list;
      }).catch(res => {
        this.loading = false;
        this.$Message.error(res.msg);
      })
    },
    onClickTab(type) {
      this.userFrom.page = 1;
      this.userFrom.is_tourist = type;
      this.getList();
    },
    userGroup() {
      let data = {
        page: 1,
        limit: ''
      };
      userGroupApi(data).then(res => {
        this.groupList = res.data.list;
        this.groupList.forEach(item => {
          this.$set(this.groupListObj, item.id, item.group_name);
        })
      })
    },
    // 批量设置分组；查询分组
    setGroup() {
      if(this.selectionList.length === 0) {
        this.$Message.warning('请选择要设置分组的用户');
      } else {
        let uids = { uids: this.array_ids };
        userSetGroup(uids).then((res) => {
          if(res.status == 200) {
            this.userGroupSelect.model = true;
            this.userGroupSelect.list = res.data;
          }
        });
      }
    },
    // 批量设置分组 - 设置分组
    handleselectUsersGroup(item) {
      userBatchGroupApi({
        ids: this.selectionList.map(item => item.id),
        group_id: item.id
      }).then(res => {
        if(res.status == 200) {
          this.userGroupSelect.model = false;
          this.getList();
          this.$Message.success(res.msg);
        }
      }).catch(rej => {
        this.$Message.error(rej.msg);
      })
    },
    // 批量设置标签；查询标签
    setLabel() {
      if(this.selectionList.length === 0) {
        this.$Message.warning('请选择要设置标签的用户');
      } else {
        let uids = { uids: this.array_ids };
        userSetLabelApi(uids).then(res => {
          if(res.status == 200) {
            this.labelGroupSelect.list = res.data;
            this.labelGroupSelect.model = true;
          }
        })
        // this.$modalForm(userSetLabelApi(uids)).then(() => this.$refs.sends.getList());
      }
    },
    // 批量设置标签，确认
    handleEditLabelGroup(labelList) {
      let ids = this.selectionList.map(item => item.id); // 用户id
      let label_id = [] // 选中的标签id
      let un_label_id = [] // 取消的标签id
      let postObject = {};
      labelList.forEach(item => {
        if(item.label && item.label.length) {
          item.label.forEach(val => {
            if(val.disabled) {
              label_id.push(val.id);
            } else {
              un_label_id.push(val.id);
            }
          })
        }
      });
      postObject = {
        ids, label_id, un_label_id
      };

      putUserLabel('', postObject).then(res => {
        if(res.status == 200) {
          this.getList();
          this.$Message.success(res.msg);
          this.labelGroupSelect.model = false;
        }
      }).catch(rej => {
        this.$Message.error(rej.msg);
      })

    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.userFrom.time = this.timeVal.join('-');
    },
    // 操作
    changeMenu(row, name, index) {
      let uid = [];
      uid.push(row.id);
      let uids = { uids: uid };
      this.activeUserInfo = row;
      switch(name) {
        case '1':
          this.openLabel(row)
          break;
        case '2':
          userSetGroup().then(res => {
            if(res.status == 200) {
              this.getUserGroup = res.data;
              this.setUserGroupModel = true;
            }
          })
          break
      }
    },
    // 确认设置用户分组
    handleSelectGroup(item) {

      userBatchGroupApi({
        ids: [this.activeUserInfo.id],
        group_id: item.id
      }).then(res => {
        if(res.status == 200) {
          this.setUserGroupModel = false;
          this.getList();
          this.$Message.success('设置成功');
        }
      }).catch(rej => {
        this.$Message.error(rej.msg);
      })

    },
    openLabel(row) {
      this.labelShow = true
      this.labelActive.uid = row.id
    },
    editS(row) {
      this.promoterShow = true;
      this.formInline.id = row.id;
    },
    customer() {
      this.customerShow = true
    },
    imageObject(e) {
      this.customerShow = false;
      this.formInline.spread_uid = e.uid;
      this.formInline.image = e.image;
    },
    cancel(name) {
      this.promoterShow = false;
      this.$refs[name].resetFields();
    },
    // 删除
    del(row, tit, num, name) {
      let delfromData = {
        title: tit,
        num: num,
        url: name === 'user' ? `user/del_level/${row.uid}` : `agent/stair/delete_spread/${row.uid}`,
        method: name === 'user' ? 'DELETE' : 'PUT',
        ids: ''
      }
      this.$modalSure(delfromData).then((res) => {
        this.$Message.success(res.msg);
        this.getList();
      }).catch(res => {
        this.$Message.error(res.msg);
      });
    },
    // 清除会员删除成功
    submitModel() {
      this.getList();
    },
    // 会员列表
    getList() {
      let userFrom = JSON.parse(JSON.stringify(this.userFrom));
      if(this.user_type === 'all'){
        userFrom.user_type = '';
      }else{
        this.userFrom.user_type = this.user_type
      }
      if(userFrom.is_tourist === 'all'){
        userFrom.is_tourist = '';
      }
      userFrom.status = userFrom.status || '';
      userFrom.sex = userFrom.sex || '';
      userFrom.label_id = this.label_id === 'all' ? '' : this.label_id.join(',');
      userFrom.field_key = this.field_key === 'all' ? '' : this.field_key;
      userFrom.group_id = this.group_id === 'all' ? '' : this.group_id;
      this.loading = true;
      userList(userFrom).then(async res => {
        let data = res.data;
        this.userLists = data.list;
        console.log(this.userLists);
        this.total = data.count;
        this.loading = false;
      }).catch(res => {
        this.loading = false;
        this.$Message.error(res.msg);
      })
    },
    pageChange(index) {
      this.selectionList = [];
      this.userFrom.page = index
      this.getList();
    },
    // 搜索
    userSearchs() {
      this.userFrom.page = 1;
      this.getList();
    },
    // 重置
    reset(name) {
      this.userFrom = {
        user_type: '',
        status: '',
        sex: '',
        time: '',
        nickname: '',
        field_key: '',
        group_id: '',
        label_id: '',
        page: 1, // 当前页
        limit: 20 // 每页显示条数
      };
      this.field_key = '';
      this.group_id = '';
      this.label_id = '';
      this.timeVal = [];
      this.getList();
    },
    // 获取编辑表单数据
    getUserFrom(id) {
      getUserData(id).then(async res => {
        if(res.data.status === false) {
          return this.$authLapse(res.data);
        }
        this.FromData = res.data;
        this.$refs.edits.modals = true;
      }).catch(res => {
        this.$Message.error(res.msg);
      })
    },
    // 修改状态
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        status: row.status
      }
      isShowApi(data).then(async res => {
        this.$Message.success(res.msg);
      }).catch(res => {
        this.$Message.error(res.msg);
      })
    },
    // 全选
    onSelectTab(selection) {
      this.selectionList = selection;
      let data = [];
      this.selectionList.map((item) => {
        data.push(item.id)
      });
      this.array_ids = data;
      this.user_ids = data.join(',');
    },
    // 编辑
    edit(row) {
      this.getUserFrom(row.id);
    },
    // 修改成功
    submitFail() {
      // this.getList();
    },
    // 排序
    sortChanged(e) {
      this.userFrom[e.key] = e.order
      this.getList();
    }
  }
}
</script>

<style scoped lang="stylus">
.picBox {
  display: inline-block;
  cursor: pointer;

  .upLoad {
    width: 58px;
    height: 58px;
    line-height: 58px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.02);
  }

  .pictrue {
    width: 60px;
    height: 60px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    margin-right: 10px;

    img {
      width: 100%;
      height: 100%;
    }
  }
}

.userFrom {
  >>> .ivu-form-item-content {
    margin-left: 0px !important;
  }
}

.userAlert {
  margin-top: 20px;
}

.userI {
  color: #1890FF;
  font-style: normal;
}

img {
  height: 36px;
  display: block;
}

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

.tabBox_tit {
  width: 60%;
  font-size: 12px !important;
  margin: 0 2px 0 10px;
  letter-spacing: 1px;
  padding: 5px 0;
  box-sizing: border-box;
}

.modelBox {
  >>> .ivu-modal-body {
    padding: 0 16px 16px 16px !important;
  }
}

.vipName {
  color: #dab176;
}

.listbox {
  >>>.ivu-divider-horizontal {
    margin: 0 !important;
  }
}
</style>
