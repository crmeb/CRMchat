<template>
  <div class="pc_customerServer_container">
    <!-- 客服头部开始 -->
    <div class="pc_customerServer_container_header">
      <div class="pc_customerServer_container_header_title">
        <img :src="chatServerData.to_user_avatar" alt="">
        <span>{{chatServerData.to_user_nickname}}</span>
      </div>
      <div class="pc_customerServer_container_header_handle" @click="closeIframe" v-if="upperData.noCanClose != '1'">
        <span class="iconfont">&#xe6c5;</span>
      </div>
    </div>
    <!-- 客服头部结束 -->

    <!-- 聊天内容开始 -->
    <div class="pc_customerServer_container_content">
      <div class="productMessage_container" v-if="isShowProductModel">
        <div class="productMessage_container_image">
          <img :src="productMessage.image" alt="">
        </div>
        <div class="productMessage_container_content">
          <div class="productMessage_container_content_title">{{productMessage.store_name}}</div>
          <div class="productMessage_container_content_priceOrHandle">
            <div>￥{{productMessage.price}}</div>
            <div @click="sendProduct">发送客服</div>
          </div>
        </div>

      </div>
      <happy-scroll size="1" resize hide-horizontal :scroll-top="scrollTop" @vertical-start="scrollHandler">
        <div class="scroll_content" id="chat_scroll" :class="{ 'pt140': isShowProductModel }">
          <!-- 滑动到容器顶部时，动画加载 -->
          <Spin v-show="isLoad">
            <Icon type="ios-loading" size=18 class="demo-spin-icon-load"></Icon>
            <div>Loading</div>
          </Spin>
          <!-- 动画结束 -->

          <!-- 聊天内容列表 -->
          <div class="chart_list">
            <div class="chart_list_item" v-for="(item, index) in records" :key="index">
              <div class="chart_list_item_time" v-show="item.show">{{item.time}}</div>
              <div class="chart_list_item_content" :class="{'right-box': item.user_id == chatServerData.user_id}">
                <div class="chart_list_item_avatar">
                  <img :src="item.avatar" alt="">
                </div>
                <!-- 文字及表情信息 -->
                <div class="chart_list_item_text" v-if="item.msn_type <= 2">
                  <span v-html="replace_em(item.msn)"></span>
                </div>
                <!-- 图片信息 -->
                <div class="chart_list_item_img" v-if="item.msn_type == 3">
                  <img v-lazy="item.msn" @load="imageLoad" />
                </div>
                <!-- 图文信息 -->
                <div class="chart_list_item_imgOrText" v-if="item.msn_type == 5">
                  <div class="order-wrapper">
                    <div class="img-box">
                      <img :src="item.other.image" alt="">
                    </div>
                    <div class="order-info">
                      <div class="price-box">
                        <div class="num">¥ {{item.other.price}}</div>
                        <!-- <a herf="javascript:;" class="more" @click.stop="lookGoods(item)">查看商品 ></a> -->
                      </div>
                      <div class="name">{{item.other.store_name}}</div>
                    </div>

                  </div>
                </div>
              </div>

            </div>

          </div>
          <!-- 聊天内容列表结束 -->
        </div>
      </happy-scroll>
    </div>
    <!-- 聊天内容结束 -->

    <!-- 内容输入开始 -->

    <div class="footer_customerServer_container">
      <div class="mobel_customerServer_container_footer">
        <div class="crmchat_link" @click="tolink">
          <span>CRMChat开源客服系统</span>
        </div>
        <div class="mobel_customerServer_container_footer_uploag_image">
          <span class="iconfont">&#xe6ca;</span>
          <input type="file" class="file_input" @change="uploadFile">
        </div>
        <div class="mobel_customerServer_container_footer_input">
          <div class="mobel_customerServer_container_footer_input_con">
            <textarea @keyup.enter="sendText" @focus="textareaInput" class="font" @input="textareaChange($event)" v-model='userMessage' placeholder="请输入内容"></textarea>
            <p class="font" v-html='pCont'></p>
          </div>
          <!-- <div class="mobel_customerServer_container_footer_input_send" @click="sendText">
            <span class="iconfont primary_color">&#xe6bb;</span>
          </div> -->
        </div>

        <!-- 选择表情 -->
        <div class="mobel_customerServer_container_footer_emoji" @click="selectEmoji">
          <span class="iconfont ">&#xe6cb;</span>
        </div>
        <!-- 发送消息 -->
        <div class="sendMessage" :class="{'sendMessage-primary': userMessage}">
          <div @click="sendText">发送</div>
        </div>
      </div>
      <!-- 表情及图片容器 -->
      <div class="mobel_customerServer_container_footer_emojiList" :class="{'canSelectemoji': inputConType == 2}">
        <div class="emoji-item" v-for="(emoji, index) in emojiList" :key="index" v-if="inputConType == 2">
          <i class="em" :class="emoji" @click.stop="select(emoji)"></i>
        </div>
      </div>
      <!-- 表情及图片容器结束 -->
    </div>
    <!-- 内容输入结束 -->

  </div>
</template>
<script>
import { HappyScroll } from 'vue-happy-scroll'
import emojiList from "@/utils/emoji";
import socketServer from './minix/socketServer';
export default {
  components: {
    HappyScroll
  },
  mixins: [socketServer],
  data() {
    return {
      isLoad: false,
      scrollTop: 0,
      emojiList: emojiList,
      pCont: ''
    }
  },
  created() {
    // this.connentServer(); // 连接webSocket 服务 [mixins 方法]
    // this.getUserRecord(); // 查看当前是否有客服在线
  },
  computed: {
    records() {
      return this.chatServerData.serviceList.map((item, index) => {
        item.time = this.$moment(item.add_time * 1000).format('MMMDo H:mm')
        if(index) {
          if(
            item.add_time -
            this.chatServerData.serviceList[index - 1].add_time >=
            300
          ) {
            item.show = true;
          } else {
            item.show = false;
          }
        } else {
          item.show = true;
        }
        return item;
      });
    }
  },
  methods: {

    textareaChange(e) {
      let strCont = e.target.value.replace(/\n\s(\s)*/gi, '\n') // 将多个回车换行合并为 1个
      strCont = strCont.replace(/^\s*/gi, '') // 清除首行的 空格与换行

      let strHtml = strCont.replace(/</gi, '&lt;')  // 将所有的 < 转义为 &lt; 防止html标签被转义
      strHtml = strCont.replace(/\n(\n)*/gi, '<br>')  // 回车换行替换为 <br>
      strHtml = strHtml.replace(/\s+/gi, '&nbsp;')  // 一个或过个空格 替换为 &nbsp;

      strCont = strHtml.replace(/&nbsp;/gi, ' ')  // 逆向处理
      strCont = strCont.replace(/<br>/gi, '\n')   // 逆向处理
      strCont = strCont.replace(/&lt;/gi, '<')

      this.userMessage = strCont
      /** 如果 p 标签最后的字符为 <br> 并不会单独另起一行, 会导致与 textarea 的高度相差一行,
        * 所以在最后添加一个字符, 这样就能保证 P 标签的高度与 textarea 的高度一致
        */
      this.pCont = strHtml + '.'
    },
    getScrollTop() {
      console.log(123);
    },
    getScrollEnd() {
      console.log(321);
    },


    // 聊天表情转换
    replace_em(str) {
      str = str.replace(/\[em-([a-z_]*)\]/g, "<span class='em em-$1'/></span>");
      return str;
    },
  }
}
</script>
<style lang="less" scoped>
.pc_customerServer_container {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background: #f5f5f5;
  &_header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(270deg, #1890ff 0%, #3875ea 100%);
    padding: 7px 14px;
    font-size: 16px;
    color: #fff;
    &_title {
      display: flex;
      align-items: center;
      img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
      }
    }

    &_handle {
      cursor: pointer;
    }
  }

  &_content {
    flex: 1;
    overflow: hidden;
    .scroll_content {
      width: 98%;
      height: 100%;
      overflow-y: auto;
      padding-bottom: 20px;
      box-sizing: border-box;
      .chart_list {
        position: relative;
        z-index: 2;
        &_item {
          &_content {
            display: flex;
            align-items: center;
            padding: 8px;
          }

          &_avatar {
            width: 33px;
            height: 33px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 10px;
            align-self: flex-start;
            img {
              width: 100%;
              height: 100%;
            }
          }
          &_text {
            max-width: 60%;
            word-wrap: break-word;
            background: #fff;
            padding: 9px 14px;
            font-size: 15px;
            border-radius: 6px;
          }
          &_img {
            max-width: 60%;
            img {
              width: 100%;
              height: auto;
            }
          }
          .chart_list_item_imgOrText {
            background: #fff;
            padding: 4px 10px;
            border-radius: 8px;
            width: 226px;
            box-sizing: border-box;
            .order-wrapper {
              .img-box {
                width: 100%;
                img {
                  width: 100%;
                  height: auto;
                }
              }
              .order-info {
                .price-box {
                  color: #ff0000;
                  font-size: 18px;
                }
                .name {
                  font-size: 14px;
                }
              }
            }
          }

          &_time {
            text-align: center;
            margin: 10px auto;
          }
          .right-box {
            flex-direction: row-reverse;
            .chart_list_item_avatar {
              margin-left: 10px;
            }
            .chart_list_item_text {
              text-align: right;
              background: #cde0ff;
              color: #000;
            }
            .chart_list_item_img {
              text-align: right;
              background: #fff;
              img {
                width: 100%;
                height: auto;
              }
            }
            .chart_list_item_imgOrText {
              background: #fff;
              padding: 10px;
              border-radius: 8px;
              width: 226px;
              box-sizing: border-box;
              .order-wrapper {
                .img-box {
                  width: 100%;
                  img {
                    width: 100%;
                    height: auto;
                  }
                }
                .order-info {
                  .price-box {
                    color: #ff0000;
                    font-size: 18px;
                  }
                  .name {
                    font-size: 14px;
                  }
                }
              }
            }
          }
          &_text {
          }
        }
      }
    }
  }

  .mobel_customerServer_container_footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid #ececec;
    padding: 8px 15px;
    box-sizing: border-box;
    position: relative;
    .crmchat_link {
      text-align: center;
      width: 98%;
      transition: 0.3s;
      z-index: 1;
      cursor: pointer;
      position: absolute;
      bottom: 60px;
      span {
        color: #ccc;
      }

      span:hover {
        color: #007aff;
      }
    }
    .iconfont {
      font-size: 24px;
    }
    .mobel_customerServer_container_footer_uploag_image {
      font-size: 24px;
      display: flex;
      align-items: center;
      position: relative;
    }
    .file_input {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
    }
    &_input {
      flex: 1;
      margin: 0 9px;
      background: #fff;
      border-radius: 4px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-sizing: border-box;
      // line-height: 28px;

      &_con {
        flex: 1;
        position: relative;
        min-height: 32px;
        display: flex;
        align-items: center;
        .font {
          // font-size: 14px;
          padding: 4px 8px;
        }
        textarea {
          display: block;
          position: absolute;
          top: 0;
          left: 0;
          z-index: 10;
          resize: none;
          outline: none;
          background: none;
          color: rgba(0, 0, 0, 0.7);
          border: none;
          width: 100%;
          height: 100%;
          font-size: 15px;
        }
        p {
          width: 100%;
          display: block;
          min-height: 20px;
          opacity: 0;
        }
      }

      &_send {
        align-self: flex-start;
        padding-right: 8px;
      }
    }

    &_emoji {
      display: flex;
      align-items: center;
      font-size: 24px;
    }
    .sendMessage {
      background: #ccc;
      padding: 4px 12px;
      border-radius: 4px;
      margin-left: 8px;
      color: #fff;
      font-size: 14px;
    }
    .sendMessage-primary {
      background: #3875ea;
    }
    &_emojiList {
      width: 100%;
      height: 0;
      // padding: 10px;
      display: grid;
      grid-template-columns: repeat(9, 1fr);
      grid-row-gap: 20px;
      overflow-y: auto;
      background: #fff;
      transition: 0.3s;
    }
  }
}
.canSelectemoji {
  height: 165px !important;
  padding: 10px;
}

.demo-spin-icon-load {
  animation: ani-demo-spin 1s linear infinite;
}

.primary_color {
  color: #3875ea;
}
.productMessage_container {
  // height: 94px;
  width: 100%;
  padding: 12px;
  box-sizing: border-box;
  background: #fff;
  display: flex;
  justify-content: space-between;
  &_image {
    margin-right: 12px;
    img {
      width: 77px;
      height: 77px;
    }
  }
  &_content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    &_title {
      font-size: 14px;
      color: #333;
      height: 42px;
      font-weight: 800;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      text-align: left !important;
    }
    &_priceOrHandle {
      display: flex;
      justify-content: space-between;
      > div:nth-child(1) {
        font-size: 18px;
        color: #e93323;
        text-align: left;
      }
      > div:nth-child(2) {
        width: 65px;
        height: 25px;
        background: #e83323;
        opacity: 1;
        border-radius: 62px;
        color: #fff;
        font-size: 12px;
        text-align: center;
        line-height: 25px;
        cursor: pointer;
      }
    }
  }
}
.pt140 {
  padding-bottom: 140px !important;
}
@keyframes ani-demo-spin {
  from {
    transform: rotate(0deg);
  }

  50% {
    transform: rotate(180deg);
  }

  to {
    transform: rotate(360deg);
  }
}
/deep/ .happy-scroll-content {
  width: 100%;
  box-sizing: border-box;
}
</style>
