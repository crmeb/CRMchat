 <template>
  <div class="footer_container">
    <div class="footer_container_item" v-for="(item, index) in footerTapList" :key="index" @click="selectTap(item)">
      <img class="image_auto" :src="item.selectImg" alt="" v-if="item.id == selectedId">
      <img class="image_auto" :src="item.img" alt="" v-else>
      <div class="title">{{item.title}}</div>
      <div class="tip" v-if="item.type == 'chat'">12</div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedId: 1,
      footerTapList: [
        {
          id: 1,
          type: 'chat',
          img: require('@/assets/images/kefu-bottom/chat_default.png'),
          selectImg: require('@/assets/images/kefu-bottom/chat.png'),
          title: '聊天',
          path: 'customerService'
        },
        {
          id: 2,
          type: 'user',
          img: require('@/assets/images/kefu-bottom/user_default.png'),
          selectImg: require('@/assets/images/kefu-bottom/user.png'),
          title: '客户',
          path: 'customerServiceUser'
        },
        {
          id: 3,
          type: 'static',
          img: require('@/assets/images/kefu-bottom/static_default.png'),
          selectImg: require('@/assets/images/kefu-bottom/static.png'),
          title: '统计',
          path: 'customerServerStatistics'
        },
        {
          id: 4,
          type: 'my',
          img: require('@/assets/images/kefu-bottom/my_default.png'),
          selectImg: require('@/assets/images/kefu-bottom/my.png'),
          title: '我的',
          path: 'my'
        }
      ]
    }
  },
  created() {
    this.selectedId = this.footerTapList.find(item => item.path == this.$route.name).id;
  },
  methods: {
    selectTap(item) {
      this.selectedId = item.id;
      this.$router.push({ name: item.path });
    }
  }
}
</script>

<style lang="less" scoped>
.footer_container {
  padding: 0.11rem 0.65rem;
  display: flex;
  justify-content: space-between;
  box-shadow: 0rem 0rem 0rem rgba(0, 33, 96, 0.04);
  border-top: 1px solid rgba(0, 33, 96, 0.04);
  background: #fff;
  &_item {
    position: relative;

    .image_auto {
      width: 0.44rem;
      height: auto;
    }
    .title {
      text-align: center;
      color: #707070;
    }
    .tip {
      position: absolute;
      top: -0.05rem;
      right: -0.3rem;
      width: 0.5rem;
      height: 0.35rem;
      background: #f74c31;
      opacity: 1;
      border-radius: 1rem;
      font-size: 10px;
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
    }
  }
}
</style>>
