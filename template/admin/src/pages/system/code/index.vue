<template>
  <div class="getCode_container">
    <div class="content">
      <Tabs value="name1">
        <TabPane label="网页内嵌" name="name1">
          <wangye :tokeninfo="token" :siteUrl="siteUrl" @cgetCopy='getCopy'></wangye>
        </TabPane>
        <TabPane label="超链接" name="name2">
          <alink :tokeninfo="token" :siteUrl="siteUrl" @cgetCopy='getCopy'></alink>
        </TabPane>
        <TabPane label="定制开发" name="name3">
          <kaifa :tokeninfo="token" :siteUrl="siteUrl" @cgetCopy='getCopy'></kaifa>
        </TabPane>
        <TabPane label="重置token" name="name4">
          <setting :tokeninfo="token" :siteUrl="siteUrl" @cgetCopy='getCopy' @cresetToken="resetToken"></setting>
        </TabPane>
      </Tabs>
    </div>
    <Modal v-model="canfrime" title="提示" @on-ok="confirme" @on-cancel="cancel">
      <div class="ivu-modal-confirm">
        <img class="modimg" src="@/assets/images/warring.png" alt="">
        <div>
          token重置后，数据将全部更新，历史数据将会失效，请问是否确定？
        </div>
      </div>
    </Modal>
  </div>

</template>

<script>
import { mapState } from 'vuex';
import { adminAppCustomer, appReset } from '@/api/kefu';
import alink from './components/alink';
import wangye from './components/wangye';
import kaifa from './components/kaifa';
import setting from './components/setting';


export default {
  name: 'setting_user',
  components: {
    alink,
    wangye,
    kaifa,
    setting
  },
  computed: {
    ...mapState('media', [
      'isMobile'
    ]),
    ...mapState('userLevel', [
      'categoryId'
    ]),
    labelWidth() {
      return this.isMobile ? undefined : 75
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'left'
    },
    linkUrl() {
      return `${location.origin}/chat/index?token=${this.token.token_md5}&noCanClose=1`;
    }
  },
  data() {
    return {
      token: '',
      canfrime: false,
      srcUrl: `${location.origin}/customerServer.js`,
      siteUrl: `${location.origin}`,
      cloneTip: false,
      canCustomerServer: ''
    }
  },

  mounted() {
    this.getAdminAppCustomer();
  },
  methods: {
    // 获取token
    getAdminAppCustomer() {
      adminAppCustomer().then(res => {
        if(res.status == 200) {
          if(res.data.list.length) {
            this.token = res.data.list[0];
          }
        }
      })
    },

    // 重置token
    resetToken() {
      this.canfrime = true;
    },
    // 确定重置token
    confirme() {
      appReset(this.token.id).then(res => {
        if(res.status == 200) {
          this.$set(this.token, 'token', res.data.token);
        }
      })
    },
    cancel() { },
    getCopy(id) {
      let content = this.copyToClipboard(document.getElementById(id))
      if(content) this.cloneTip = true
    },
    copyToClipboard(elem) {
      // create hidden text element, if it doesn't already exist
      var targetId = "_hiddenCopyText_";
      var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
      var origSelectionStart, origSelectionEnd;
      if(isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
      } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if(!target) {
          var target = document.createElement("textarea");
          target.style.position = "absolute";
          target.style.left = "-9999px";
          target.style.top = "0";
          target.id = targetId;
          document.body.appendChild(target);
        }
        target.textContent = elem.textContent;
      }
      // select the content
      var currentFocus = document.activeElement;
      target.focus();
      target.setSelectionRange(0, target.value.length);

      // copy the selection
      var succeed;
      try {
        succeed = document.execCommand("copy");
      } catch(e) {
        succeed = false;
      }
      // restore original focus
      if(currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
      }

      if(isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
      } else {
        // clear temporary content
        target.textContent = "";
      }

      this.$Message.success('已成功复制到粘贴板');

      return succeed;
    }
  }
}
</script>

<style lang="less">
.getCode_container {
  .content {
    width: 100%;
    color: #323437;
    background: #ffffff;
    margin-top: 18px;
    font-size: 13px;
    padding: 10px;
  }
  .font-w {
    font-weight: 800;
    margin: 10px 0;
  }

  .text-i {
    text-indent: 2em;
  }

  .content > p {
    margin-bottom: 6px;
  }

  .code-content-wrap {
    clear: both;
    border: 1px solid #e4e4e4;
    border-radius: 3px;
    padding: 12px 17px;
    background-color: #f8f8f8;
  }

  .other-wrap {
    margin: 4px 0;
    text-align: right;
  }

  .textarea {
    border: none;
    /* height: 40px; */
    width: 100%;
    outline: 0;
    resize: none;
    background-color: #f8f8f8;
    font-family: Arial;
    color: #323437;
    line-height: 24px;
    text-align: left;
  }

  .code {
    border: none;
    /* height: 40px; */
    width: 100%;
    outline: 0;
    resize: none;
    background-color: #f8f8f8;
    font-family: Arial;
    color: #323437;
    line-height: 24px;
    text-align: left;
  }

  .btn {
    display: inline-block;
    zoom: 1;
    padding: 6px 16px;
    border: 1px solid #d9dbdc;
    border-radius: 2px;
    line-height: 1;
    color: #323437;
    cursor: pointer;
    outline: 0;
  }

  .btn.btn-blue {
    color: #fff;
    background-color: #4f97e7;
    border-color: #3085e3;
  }

  .setting-highlight {
    color: #f15755;
    margin-left: 5px;
    line-height: 30px;
  }
  .fenlei {
    margin: 10px 0;
    border: 1px solid #eee;
    padding: 30px;
    padding-bottom: 10px;
    border-radius: 6px;
  }
  .typetitle {
    padding: 4px 7px;
    font-size: 18px;
  }
}

</style>
<style  scoped>
  .ivu-modal-confirm {
    display: flex;
    align-items: center;

  }
  .modimg {
    width: 40px !important;
    height: 40px !important;
    margin-right: 30px;
  }
</style>
