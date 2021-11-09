<template>
  <div class="customerOutLine_server" :class="{ 'max_style': !isMobile }">
    <div class="customerOutLine_server_header">
      <span>商城客服已离线</span>
      <div class="pc_customerServer_container_header_handle" @click="closeIframe">
        <span class="iconfont">&#xe6c6;</span>
      </div>
    </div>
    <div class="customerOutLine_server_content">
      <div class="customerOutLine_server_content_message" v-html="feedback">
        <div>您好，现在客服不在线，请留言。如果没有留下您的联系方式，客服将无法和您联系！</div>
        <div class="customerOutLine_server_content_message_phone">
          <div>
            我们的工作时间：09:00～22:00
          </div>
          <div>
            售前客服电话：400-8888-794
          </div>
        </div>
      </div>

      <div class="customerOutLine_server_content_form">
        <div>
          <input v-model="feedData.rela_name" type="text" placeholder="请输入您的姓名">
        </div>
        <div>
          <input v-model="feedData.phone" type="number" placeholder="请输入您的联系电话">
        </div>
        <div>
          <textarea v-model="feedData.content" name="" id="" cols="30" rows="10" placeholder="请填写留言内容"></textarea>
        </div>
      </div>

      <div class="customerOutLine_server_content_handle">
        <div @click="postFeedMessage">
          <span>提交留言</span>
        </div>
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
    console.log('outline')
    this.selectFeedBack();
    parent.postMessage({ type: 'customerOutLine' }, "*"); // 通知客服已经离线
    console.log(this.$route.query);
  },
  methods: {
    // 查询广告
    selectFeedBack() {
      serviceFeedback().then(res => {
        if(res.status == 200) {
          this.feedback = res.data.feedback;
        }
      })
    },
    // 提交反馈信息
    postFeedMessage() {
      serviceFeedbackPost(this.feedData).then(res => {
        if(res.status == 200) {
          this.$Message.success('提交成功');
          this.$router.push({
            name: 'finishSubmitOutLine',
            query: this.$route.query
          })
        }
      }).catch(rej => {
        this.$Message.error(rej.msg);
      })
    },
    // 关闭弹框
    closeIframe() {
      parent.postMessage({ type: 'closeWindow' }, "*");
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
    &_message {
      font-size: 13px;
      color: #333333;
      margin-bottom: 26px;
      &_phone {
        margin-top: 30px;
      }
    }
    &_form {
      > div {
        margin-bottom: 18px;
        input {
          border-radius: 3px;
          border: 1px solid #ececec;
          outline: none;
          font-size: 13px;
          padding-left: 14px;
          width: 100%;
          height: 40px;
        }
        textarea {
          width: 100%;
          border: 1px solid #ececec;
          outline: none;
          font-size: 13px;
          padding: 14px;
        }
      }
    }

    &_handle {
      margin-top: 13px;
      > div {
        height: 40px;
        background: #3875ea;
        border-radius: 3px;
        color: #fff;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
    }
  }
}
.max_style {
  max-width: 375px;
  max-height: 668px;
}
</style>