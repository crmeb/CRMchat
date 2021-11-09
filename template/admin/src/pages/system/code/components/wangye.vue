<template>
  <div class="content">
    <p class="font-w">使用简介</p>
    <p class="text-i">网页内快速接入客服，让网页拥有客服窗口，请把一下代码复制到网页最底部。</p>
    <Divider />
    <p class="typetitle">获取代码</p>

    <div class="fenlei">
      <div class="code-content-wrap">
        <textarea id="NormalCodeTextarea1" class="code" rows="22">
<script>
  (function() {
  _s = document.createElement('script');
  _s.src="{{siteUrl}}/customerServer.js"
  _s.onload = function(){
  var option = {
        "authInit":true,
        openUrl: '{{siteUrl}}',
        token: '4109fbb2d7bc3d5559348278816a20bc',
        kefuid:'',//默认为空自动对接客服，可填写指定客服ID
        isShowTip: true, // 初始化成功后，界面右下角会自动创建 “联系客服按钮”， 如无需默认展示，则填写false即可,默认为true
        mobileIcon: '', //  手机端悬浮客服图片
        pcIcon: '', // pc端悬浮客服图片
        windowStyle:'center',//默认空 右下角小弹窗， center 普通中间弹窗样式
      };
      var canCustomerServer = new initCustomerServer(option);
      canCustomerServer.init();
  }
  document.head.appendChild(_s)
  })();
</script>
            </textarea>
        <div class="other-wrap">
          <a class="btn btn-blue btn-large mr10" @click="jiazai" href="javascript:void(0);">点击体验</a>
          <a @click="getCopy('NormalCodeTextarea1')" class="btn btn-blue btn-large" href="javascript:void(0);"><span>复制代码</span></a>
        </div>
      </div>

    </div>


    <div class="fenlei">
      <p class="font-w">小贴士</p>
      <p class="text-i">如果点击体验，提示客服不在线，请进入客服点击进入客服登录一个账号再试。</p>
      <p class="text-i">如需更换token，请在设置中重新获取。</p>
    </div>
  </div>
</template>
<script>
import { mapState } from 'vuex';
import { adminAppCustomer, appReset } from '@/api/kefu';
import initCustomerServer from '@/libs/customerServer';
export default {
  name: 'wangye',
  props: {
    tokeninfo: {},
    siteUrl: '',
  },
  mounted() {

  },
  methods: {
      jiazai(){
          window.open(`${location.origin}/chat/index?token=${this.tokeninfo.token_md5}&noCanClose=1`)
      },
    getCopy(id) {
      this.$emit('cgetCopy', id);
    },
  }
}
</script>
<style lang="less" scoped>
.a {
  display: none;
  display: block;
  border-radius: 8px;
  overflow: hidden;
}
</style>

