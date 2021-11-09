<template>
  <div class="right-wrapper">
    <template>
      <div class="user-wrapper" v-if="activeUserInfo">
        <div class="user">
          <div class="avatar"><img v-lazy="activeUserInfo.avatar" alt=""></div>
          <div class="name line1">
            <span v-if="editUserNameModel == false" @click="editUserNameModel= true">{{activeUserInfo.remark_nickname?activeUserInfo.remark_nickname: activeUserInfo.nickname}}</span>
            <Input v-if="editUserNameModel" v-model="activeUserInfo.nickname" placeholder="请输入昵称" @on-blur="editUserData('editUserNameModel')"></Input>
          </div>
          <div class="label">
            <template v-if="webType == 2">
              <span class="label routine">小程序</span>
            </template>
            <template v-if="webType == 3">
              <span class="label H5">H5</span>
            </template>
            <template v-if="webType == 1">
              <span class="label wechat">公众号</span>
            </template>
            <template v-if="webType == 0">
              <span class="label pc">PC端</span>
            </template>
          </div>
        </div>
        <div class="user-info">
          <div class="item">
            <span>电话</span>
            <span @click="editUserPhoneModel=true" v-if="!editUserPhoneModel">{{activeUserInfo.phone || '暂无'}}</span>
            <Input v-else v-model="activeUserInfo.phone" @on-blur="editUserData('editUserPhoneModel')" placeholder="暂无手机号" size="small"></Input>
          </div>

          <!-- <div class="item">
            <span>分组</span>

            <Select v-model="activeUserInfo.group_id" size="small" @on-change="onChange" style="flex:1;">
              <Option v-for="item in userGroupList" :value="item.id" :key="item.value">{{ item.group_name }}</Option>
            </Select>
          </div> -->

          <div class="label-list" @click="isUserGroup = true">
            <span>分组</span>
            <div class="con">
              <div class="label-item" v-if="activeUserInfo.group_id">{{userGroupList.find(item => item.id == copyGroupId).group_name}}</div>
            </div>
            <div class="right-icon">
              <Icon type="ios-arrow-forward" size="14" />
            </div>
          </div>

          <div class="label-list" @click.stop="isUserLabel = true">
            <span>标签</span>
            <div class="con">
              <div class="label-item" v-for="item in activeUserInfo.label">{{item.label}}</div>
            </div>
            <div class="right-icon">
              <Icon type="ios-arrow-forward" size="14" />
            </div>
          </div>

          <div class="label-list" @click.stop="isEditRemark = true;remarkValue = activeUserInfo.remarks;">
            <span>备注</span>
            <div class="con">
              <div class="">{{activeUserInfo.remarks}}</div>
            </div>
            <div class="right-icon">
              <Icon type="ios-arrow-forward" size="14" />
            </div>
          </div>

        </div>
        <!-- <div class="user-info">
          <div class="item">
            <span>用户等级</span>
            {{activeUserInfo.level_name}}
          </div>
          <div class="item">
            <span>推荐人</span>
            {{activeUserInfo.spread_name}}
          </div>
          <div class="item">
            <span>用户类型</span>
            {{activeUserInfo.user_type | typeFilters}}
          </div>
          <div class="item">
            <span>余额</span>
            {{activeUserInfo.now_money}}
          </div>
          <div class="item">
            <span>推广员</span>{{activeUserInfo.is_promoter?'是':'否'}}
          </div>
          <div class="item">
            <span>生日</span>
            {{activeUserInfo.birthday | getDay}}
          </div>
        </div> -->
      </div>
      <empty v-else status="2" msg="暂无用户信息"></empty>

    </template>

    <!-- 发货弹窗 -->
    <Modal v-model="isDelivery" title="订单发送货" :footer-hide="true">
      <delivery v-if="isDelivery" @close="deliveryClose" @ok="deliveryOk" :orderId="orderId"></delivery>
    </Modal>
    <!-- 订单备注 -->
    <Modal v-model="isRemarks" title="请修改内容" :footer-hide="true" :mask="true" width="520" :closable="false" class="none-radius">
      <remarks :remarkId="remarkId" v-if="isRemarks" @close="deliveryClose" @remarkSuccess="remarkSuccess"></remarks>
    </Modal>
    <!-- 用户标签 -->
    <Modal v-model="isUserLabel" :footer-hide="true" width="320" class="label-box" :closable="false" :mask="true">
      <p class="label-head" slot="header">
        <span>选择用户标签</span>
      </p>
      <userLabel v-if="isUserLabel" @close="deliveryClose" :uid="uid" @editLabel="editLabel"></userLabel>
    </Modal>
    <!-- 分组 -->
    <Modal v-model="isUserGroup" :footer-hide="true" width="320" class="label-box" :closable="false" :mask="true">
      <p class="label-head" slot="header">
        <span>选择用户分组</span>
      </p>
      <user-group v-if="isUserGroup" @close="usergroupClose" :userGroup="userGroupList" :activeUserInfo="activeUserInfo" @selectGroup="selectGroup" @handleSelectGroup="handleSelectGroup"></user-group>
    </Modal>

    <Modal v-model="isEditRemark" title="请输入用户备注" width="320" class="none-radius">
      <Input v-model="remarkValue" placeholder="请输入备注"></Input>
      <div slot="footer">
        <Button @click="isEditRemark=false">取消</Button>
        <Button type="primary" @click="handlyEditRemark">确定</Button>
      </div>
    </Modal>
  </div>
</template>

<script>
import delivery from './delivery'
import remarks from './remarks'
import userLabel from "./userLabel";
import userGroup from './userGroup';
import {
  userInfo, getorderList,
  orderEdit, orderRecord,
  productCart, productHot,
  productVisit, userGroupApi,
  putGroupApi,
  updateUserData
} from '@/api/kefu'
import empty from "../../components/empty";
import dayjs from 'dayjs'
export default {
  name: "rightMenu",
  components: {
    delivery,
    remarks,
    userLabel,
    empty,
    userGroup
  },
  props: {
    isTourist: {
      type: String | Number,
      default: 0
    },
    status: {
      type: String | Number,
      default: ''
    },
    //用户uid
    uid: {
      type: String | Number,
      default: ''
    },
    webType: {
      type: String | Number,
      default: ''
    }
  },
  filters: {
    statusFilters: function(value) {
      const statusMap = {
        '-1': '申请退款',
        '-2': '退货成功',
        '0': '待发货',
        '1': '待收货',
        '2': '已收货',
        '3': '待评价',
        '-1': '已退款',
      }
      return statusMap[value]
    },
    getDay(val) {
      if(val) {
        return dayjs.unix(val).format('YYYY年M月D日')
      }
    },
    typeFilters(value) {
      const statusMap = {
        'h5': 'H5',
        'wechat': '公众号',
        'routine': '小程序',
        'pc': 'PC',
      }
      return statusMap[value]
    }
  },
  data() {
    return {
      editUserNameModel: false,
      editUserPhoneModel: false,
      copyGroupId: '',
      isEditRemark: false, // 修改备注
      remarkValue: '',
      isUserGroup: false, // 是否展示分组
      userGroupList: [],
      model1: '',
      curMenuIndex: 0,
      menuList: [
        {
          key: '',
          title: '全部'
        },
        {
          key: 0,
          title: '未支付'
        },
        {
          key: 1,
          title: '未发货'
        },
        {
          key: -1,
          title: '退款中'
        },
      ],
      activeUserInfo: '', //用户详情
      // curStatus: this.status,
      limit: 10,
      orderConfig: {
        page: 1,
        type: '',
        searchTxt: ''
      },
      orderList: [],
      isOrderScroll: true,
      isOrderHidden: true,
      isDelivery: false, // 发货弹窗
      isRemarks: false, // 备注弹窗
      goodsTab: [
        {
          key: 0,
          title: '购买'
        },
        {
          key: 1,
          title: '足迹'
        },
        {
          key: 2,
          title: '热销'
        },
      ],
      isGoodsScroll: true,
      page: 1,
      goodsConfig: {
        type: 0,
        buyList: []
      },
      isUserLabel: false,
      remarkId: '',
      orderId: '',
      storeName: ''

    }
  },
  watch: {
    uid(nVal, oVal) {
      if(nVal != oVal) {
        this.orderConfig.page = 1
        this.isOrderScroll = true
        this.orderList = []
        this.page = 1
        this.isGoodsScroll = true
        this.goodsConfig.buyList = []
        this.getUserInfo();
      }
    },
  },
  mounted() {

    let self = this

    if(this.uid) {
      this.getUserInfo();
      // 查询客服分组列表

    }
    userGroupApi().then(res => {
      this.userGroupList = res.data;
    })

  },
  methods: {

    // 修改备注
    handlyEditRemark() {
      console.log(this.activeUserInfo);
      if(!this.remarkValue) {
        this.$Message.error('请填写用户备注');
        return;
      }
      updateUserData(this.activeUserInfo.id, { remarks: this.remarkValue }).then(res => {
        this.$Message.success('修改成功');
        this.getUserInfo();
        this.remarkValue = '';
        this.isEditRemark = false
      })
    },

    // 用户分组弹框关闭
    usergroupClose() {
      this.activeUserInfo.group_id = this.copyGroupId;
      this.isUserGroup = false;
    },
    // 选择分组
    selectGroup(item) {
      this.copyGroupId = JSON.stringify(JSON.parse(this.activeUserInfo.group_id)); // 拷贝一份原始ID
      this.activeUserInfo.group_id = item.id;
    },
    // 确定分组选择
    handleSelectGroup() {
      this.copyGroupId = JSON.stringify(JSON.parse(this.activeUserInfo.group_id)); // 确定后 id 统一
      putGroupApi(this.uid, this.activeUserInfo.group_id).then(res => {
        this.$Message.success(res.msg)
        setTimeout(() => {
          this.isUserGroup = false;
        }, 500)
      }).catch(rej => {
        this.$Message.error(rej.msg);
      })
    },
    // 设置分组
    onChange(e) {
      if(e) {
        putGroupApi(this.uid, e).then(res => {
          this.$Message.success(res.msg)
        }).catch(rej => {
          console.log(rej);
          this.$Message.error(rej.msg);
        })
      }

    },

    // 订单发货
    openDelivery(item) {
      this.orderId = item.id
      this.isDelivery = true
    },
    // 订单发货成功
    deliveryOk() {
      this.orderConfig.page = 1
      this.isOrderScroll = true
      this.orderList = []
      this.getOrderList()
      this.isDelivery = false

    },
    // 订单备注
    bindRemark(item) {
      this.remarkId = item.order_id
      this.isRemarks = true
    },
    remarkSuccess() {
      this.remarkId = ''
      this.isRemarks = false
    },
    //获取左侧用户列表用户详情
    getUserInfo() {
      userInfo(this.uid).then(res => {
        this.activeUserInfo = res.data;
        this.copyGroupId = this.activeUserInfo.group_id;
      }).catch(error => {
        this.activeUserInfo = ''
      })
    },
    // 获取订单列表
    getOrderList() {
      if(!this.isOrderScroll) return
      getorderList(this.uid, {
        page: this.orderConfig.page,
        limit: this.limit,
        type: this.orderConfig.type,
        search: this.orderConfig.searchTxt
      }).then(res => {
        this.orderConfig.page += 1
        this.isOrderScroll = res.data.length >= this.limit
        this.orderList = this.orderList.concat(res.data)
      })
    },
    // 订单tab
    bindTab(item) {
      if(this.orderConfig.type === item.key) return
      this.orderConfig.type = item.key
      if(this.uid) {
        this.orderConfig.page = 1
        this.isOrderScroll = true
        this.orderList = []
        this.getOrderList()
      }

    },
    // 订单回车
    orderSearch() {
      this.isOrderScroll = true
      this.orderList = []
      this.orderConfig.page = 1
      this.getOrderList()
    },
    // 关闭发货模态框
    deliveryClose() {
      this.isUserLabel = false
      this.isDelivery = false
      this.isRemarks = false
    },
    // 订单改价
    orderEdit(id) {
      this.$modalForm(orderEdit(id)).then(() => this.getOrderList());
    },
    // 订单退款
    orderRecord(id) {
      this.$modalForm(orderRecord(id)).then(() => this.getOrderList());
    },
    // 订单加载更多
    orderReachBottom() {
      this.getOrderList()
    },
    // 商品加载更多
    goodsReachBottom() {
      if(this.goodsConfig.type == 0) {
        this.productCart()
      } else if(this.goodsConfig.type == 1) {
        this.productVisit()
      } else {
        this.productHot()
      }
    },
    // 商品信息tab
    bindGoodsTab(item) {
      if(this.goodsConfig.type == item.key) return
      this.goodsConfig.type = item.key
      this.page = 1
      this.isGoodsScroll = true
      this.goodsConfig.buyList = []
      if(item.key == 0) {
        this.productCart()
      } else if(item.key == 1) {
        this.productVisit()
      } else {
        this.productHot()
      }
    },
    // 商品购买记录
    productCart() {
      if(!this.isGoodsScroll) return
      productCart(this.uid, {
        store_name: this.storeName,
        page: this.page,
        limit: this.limit
      }).then(res => {
        this.page += 1
        this.isGoodsScroll = res.data.length >= this.limit
        this.goodsConfig.buyList = this.goodsConfig.buyList.concat(res.data)
      })
    },
    // 商品足迹
    productVisit() {
      if(!this.isGoodsScroll) return
      productVisit(this.uid, {
        store_name: this.storeName,
        page: this.page,
        limit: this.limit
      }).then(res => {
        this.page += 1
        this.isGoodsScroll = res.data.length >= this.limit
        this.goodsConfig.buyList = this.goodsConfig.buyList.concat(res.data)
      })
    },
    // 热销商品
    productHot() {
      productHot(this.uid, {
        store_name: this.storeName,
        page: this.page,
        limit: this.limit
      }).then(res => {
        this.page += 1
        this.isGoodsScroll = res.data.length >= this.limit
        this.goodsConfig.buyList = this.goodsConfig.buyList.concat(res.data)
      })
    },
    // 修改用户标签
    editLabel() {
      this.isUserLabel = false
      this.getUserInfo()
    },
    // 商品推送
    pushGoods(item) {
      this.$emit('bindPush', item.id)
    },
    // 商品搜索
    productSearch() {
      this.page = 1
      this.isGoodsScroll = true
      this.goodsConfig.buyList = []
      if(this.goodsConfig.type == 0) {
        this.productCart()
      } else if(this.goodsConfig.type == 1) {
        this.productVisit()
      } else {
        this.productHot()
      }
    },
    // 修改用户信息
    editUserData(model) {
      this.putUserData(() => {
        this[model] = false;
      });
    },
    putUserData(callback) {
      updateUserData(this.activeUserInfo.id, { ...this.activeUserInfo, remark_nickname: this.activeUserInfo.nickname }).then(res => {
        this.$Message.success('修改成功');
        this.getUserInfo();
        this.remarkValue = '';
        this.isEditRemark = false;
        callback();
      })
    }
  }
}
</script>

<style lang="stylus" scoped>
/deep/.ivu-select .ivu-select-dropdown, /deep/.ivu-date-picker .ivu-select-dropdown {
  top: unset !important;
}

.right-wrapper {
  width: 280px;

  .user-wrapper {
    padding: 0 8px;

    .user {
      display: flex;
      align-items: center;
      padding: 16px 0;
      border-bottom: 1px solid #ECECEC;

      .avatar {
        width: 42px;
        height: 42px;

        img {
          display: block;
          width: 100%;
          height: 100%;
          border-radius: 50%;
        }
      }

      .name {
        max-width: 150px;
        margin-left: 10px;
        font-size: 16px;
        color: rgba(0, 0, 0, 0.65);
      }

      .label {
        margin-left: 5px;
        font-size: 12px;
        border-radius: 2px;
        padding: 2px 5px;

        &.H5 {
          background: #FAF1D0;
          color: #DC9A04;
        }

        &.wechat {
          background: rgba(64, 194, 73, 0.16);
          color: #40C249;
        }

        &.pc {
          background: rgba(100, 64, 194, 0.16);
          color: #6440c2;
        }
      }
    }
  }
}

color #6440C2, &.routine {
  color: #3875EA;
  background: #d8e5ff;
}

.user-info {
  padding-top: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid #ECECEC;

  .item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    font-size: 14px;
    color: #333;

    span {
      width: 70px;
      font-size: 13px;
      color: #666;
    }
  }

  .label-list {
    position: relative;
    display: flex;
    margin-bottom: 10px;

    span {
      width: 70px;
      font-size: 13px;
      color: #666;
    }

    .con {
      display: flex;
      flex-wrap: wrap;
      flex: 1;

      .label-item {
        margin-right: 8px;
        margin-bottom: 8px;
        padding: 0 5px;
        color: #1890FF;
        background: rgba(24, 144, 255, 0.1);
      }
    }

    .right-icon {
      position: absolute;
      right: 0;
      top: 0;
      cursor: pointer;
    }
  }
}

.order-wrapper {
  .tab-head {
    display: flex;
    align-items: center;
    height: 46px;
    border-bottom: 1px solid #ECECEC;

    .tab-item {
      position: relative;
      flex: 1;
      text-align: center;
      font-size: 14px;
      cursor: pointer;

      &.active {
        color: #1890FF;
        font-size: 15px;
        font-weight: 600;

        &::after {
          content: ' ';
          position: absolute;
          left: 0;
          bottom: -12px;
          width: 100%;
          height: 2px;
          background: #1890FF;
        }
      }
    }
  }

  .search-box {
    padding: 0 8px;
    margin-top: 12px;

    /deep/ .ivu-input {
      border-radius: 17px;
    }
  }

  .order-list {
    padding: 0 8px;
    margin-top: 10px;
  }

  .order-item {
    margin-bottom: 18px;

    .head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 36px;
      padding: 0 10px;
      background: #F5F5F5;
      font-size: 13px;

      .left {
        display: flex;
        align-items: center;
        color: #1890FF;

        .font-box {
          margin-right: 5px;

          .iconfont {
            font-size: 18px;
          }
        }
      }
    }

    .goods-list {
      max-height: 152px;
      overflow: hidden;

      &.auto {
        max-height: none;
      }

      .goods-item {
        display: flex;
        margin-top: 15px;

        .img-box {
          width: 60px;
          height: 60px;

          img {
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 2px;
          }
        }

        .info {
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          width: 180px;
          margin-left: 10px;
          font-size: 14px;

          .sku {
            font-size: 12px;
            color: #999999;
          }
        }
      }
    }
  }

  .more-box {
    text-align: right;
    color: #1890FF;
    font-size: 13px;
    padding-right: 10px;

    span {
      cursor: pointer;
    }
  }

  .order-info {
    margin-top: 15px;

    .info-item {
      margin-bottom: 5px;
      font-size: 13px;

      span {
        display: inline-block;
        width: 70px;
        text-align: right;
      }
    }
  }

  .btn-wrapper {
    margin-top: 10px;

    .btn {
      width: 59px;
      margin-right: 5px;

      &:last-child {
        margin-right: 0;
      }
    }
  }
}

.goods-wrapper {
  .goods-tab {
    display: flex;
    justify-content: space-between;
    padding: 0 40px;
    border-bottom: 1px solid #ECECEC;

    .tab-item {
      position: relative;
      height: 50px;
      line-height: 50px;
      font-size: 14px;
      cursor: pointer;

      &.active {
        color: #1890FF;

        &::after {
          content: ' ';
          position: absolute;
          left: 0;
          bottom: 0;
          width: 100%;
          height: 2px;
          background: #1890FF;
        }
      }
    }
  }

  .search-box {
    margin-top: 10px;
    padding: 0 8px;

    /deep/ .ivu-input {
      border-radius: 17px;
    }
  }

  .list-wrapper {
    padding: 0 8px;

    .list-item {
      display: flex;
      margin-top: 15px;

      .img-box {
        width: 60px;
        height: 60px;

        img {
          display: block;
          width: 100%;
          height: 100%;
          border-radius: 2px;
        }
      }

      .info {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 180px;
        margin-left: 10px;
        font-size: 14px;

        .sku {
          font-size: 12px;
          color: #999999;

          span {
            margin-right: 10px;
          }
        }

        .price {
          display: flex;
          justify-content: space-between;
          color: #FF0000;

          .push {
            color: #1890FF;
            cursor: pointer;
          }
        }
      }
    }
  }
}

.label-box {
  >>> .ivu-modal-header {
    padding: 0;
    border: 0;
    background: #fff;
    height: 50px;
    border-radius: 6px;
  }

  .label-head {
    height: 50px;
    line-height: 50px;
    text-align: center;
    font-size: 13px;
    color: #333333;
    border-bottom: 1px solid #F0F0F0;
  }
}
</style>
