<template>
  <div class="customerOutLine_server" :class="{ 'max_style': !isMobile }">
    <div class="customerOutLine_server_header">
      <span>商城客服已离线</span>
      <div class="pc_customerServer_container_header_handle" @click="closeIframe">
        <span class="iconfont">&#xe6c6;</span>
      </div>
    </div>

    <div class="customerOutLine_server_content">
      <div class="icon">
        <span class="iconfont">&#xe6d4;</span>
      </div>
      <div class="title">
        <span>提交成功</span>
      </div>
      <div class="message">
        <span>您的信息已提交成功，我们会尽快与您取得联系！</span>
      </div>
      <div class="button" @click="closeIframe">
        <span>好的</span>
      </div>
    </div>

  </div>
</template>
<script>
import { serviceFeedback, serviceFeedbackPost } from '@/api/kefu';
import { mapState } from 'vuex';
export default {
  data() {
    return {
      feedback: '', // 广告内容
      feedData: {
        rela_name: '',
        phone: '',
        content: ''
      }
    }
  },
  computed: {
    ...mapState('media', ['isMobile']),
  },
  created() {

  },
  methods: {
    // 查询广告

    // 关闭弹框
    closeIframe() {
      parent.postMessage({ type: 'closeWindow' }, "*");
      this.$router.push({
        name: 'customerServerRedirect',
        query: this.$route.query
      })
      parent.postMessage({ type: 'reload' }, "*");
    }
  }
}
</script>
<style lang="less" scoped>
.customerOutLine_server {
  width: 100%;
  height: 100%;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  // box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.7);
  &_header {
    height: 50px;
    background: linear-gradient(270deg, #1890ff 0%, #3875ea 100%);
    color: #fff;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 18px;
    .pc_customerServer_container_header_handle {
      cursor: pointer;
    }
  }

  &_content {
    padding: 17px 18px;
    .icon {
      width: 70px;
      height: 70px;
      margin: auto;
      background: #55d443;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      margin-top: 34px;
      .iconfont {
        color: #fff;
        font-size: 32px;
      }
    }
    .title {
      text-align: center;
      margin: 15px 0 6px 0;
      font-size: 16px;
      font-weight: bold;
    }
    .message {
      text-align: center;
      span {
        display: inline-block;
        text-align: left;
      }
    }
    .button {
      width: 160px;
      height: 40px;
      background: #3875ea;
      border-radius: 3px;
      font-size: 13px;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: auto;
      margin-top: 35px;
    }
  }
}
</style>