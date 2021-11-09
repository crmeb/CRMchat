<template>
    <div class="getCode_container">
        <div class="i-layout-page-header">
            <div class="i-layout-page-header">
                <span class="ivu-page-header-title">{{ $route.meta.title }}</span>
            </div>
        </div>
        <div class="content">
            <br>
            <p class="font-w">
                <a style="font-size: 20px" :href="linkUrl" target="_blank">
                    点击这里，开始体验
                  </a>
            </p>
            <br>
            <p class="font-w">快速上手</p>
            <p class="font-w">第一步，将js文件引入到项目中</p>
            <p class="text-i">引入方式一：如果您的项目是基于webpack或其他工具构建的，并且您不想通过操作html文件来引入js，则推荐您在入口文件中如（main.js）中写下以下代码;</p>
            <div class="code-content-wrap">
        <textarea id="NormalCodeTextarea" class="code" rows="7">
        // 该方法通过动态创建script标签后，操作dom的src属性将文件引入。
        (function() {
            var hm = document.createElement("script");
            hm.src = "{{srcUrl}}";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })()
        </textarea>
            </div>
            <div class="other-wrap">
                <a @click="getCopy('NormalCodeTextarea')" class="btn btn-blue btn-large" href="javascript:void(0);"><span>复制代码</span></a>
            </div>
            <p class="text-i">引入方式二：您可以直接在需要引入crmChat服务的页面，即html文件直接使用标签引入的方式;</p>
            <p class="text-i" style="color: #ff0000">请注意: {{srcUrl}} 文件的地址为当前管理系统所在的地址，若引入报错，请核实引入的文件地址是否正确</p>
            <div class="code-content-wrap">
        <textarea id="NormalCodeTextarea1" class="code textarea" rows="2">
          <script src="{{srcUrl}}"></script>
        </textarea>
            </div>

            <div class="other-wrap">
                <a @click="getCopy('NormalCodeTextarea1')" class="btn btn-blue btn-large" href="javascript:void(0);"><span>复制代码</span></a>
            </div>

            <p class="font-w">第二步：在所需使用crmChat服务的文件中，实例化 initCustomerServer 对象, 调用对象的 init 方法，开始加载crmChat服务</p>
            <p class="text-i">实例化对象(initCustomerServer)，并传入所需参数，基础调用示例如下，基础示例的聊天模式为游客模式。</p>
            <div class="code-content-wrap">
        <textarea id="NormalCodeTextarea2" class="code textarea" rows="15">
          <script>
            this.canCustomerServer = new initCustomerServer({
                openUrl: location.origin, // 打开客服聊天框的地址，即：部署后台管理系统的地址，若未填写，则自动获取当前服务器的地址
                type: '', //默认为空， 非必填项, 系统会自动判断启用crmChat服务的端，从而展示不同的视图，如需指定，则修改此处参数即可，即：移动端填写 "Mobile"，pc端填写 "pc"即可
                insertDomNode: '.getCode_container', // SPA应用必填，html文件单独引入选填，表示插入客服弹窗的 dom节点，一般为当前界面的根节点，默认为body
                token: {{token.token}}, // token,与后台交互的凭证
            isShowTip: true, // 初始化成功后，界面右下角会自动创建 “联系客服按钮”， 如无需默认展示，则填写false即可
            });
            this.canCustomerServer.init();
          </script>
        </textarea>
            </div>
            <div class="other-wrap">
                <a @click="getCopy('NormalCodeTextarea2')" class="btn btn-blue btn-large" href="javascript:void(0);"><span>复制代码</span></a>
            </div>
            <p class="text-i">如需传入客户信息，则需在传入initCustomerServer对象的参数中，加入 sendUserData参数， 调用示例如下 </p>

            <div class="code-content-wrap">
        <textarea id="NormalCodeTextarea3" class="code textarea" rows="24">
          <script>
            let option = {
                openUrl: location.origin, // 打开客服聊天框的地址，即：部署后台管理系统的地址，若未填写，则自动获取当前服务器的地址
                insertDomNode: '.getCode_container', // SPA应用必填，html文件单独引入选填，表示插入客服弹窗的 dom节点，一般为当前界面的根节点，默认为body
                token: {{token.token}}, // token,与后台交互的凭证
            isShowTip: true, // 初始化成功后，界面右下角会自动创建 “联系客服按钮”， 如无需默认展示，则填写false即可,默认为true
                // sendUserData为客户信息
                sendUserData: {
                uid: '1', // 用户id
                    nickName: '', // 用户昵称
                    phone: '', // 用户联系方式
                    sex: '1', // 用户性别
                    avatar: '', // 用户头像 URL地址
                    openid: ''
            }
            };
            var canCustomerServer = new initCustomerServer(option);
            canCustomerServer.init();
          </script>
        </textarea>

            </div>
            <div class="other-wrap">
                <a @click="getCopy('NormalCodeTextarea3')" class="btn btn-blue btn-large" href="javascript:void(0);"><span>复制代码</span></a>
            </div>

            <p class="text-i">如需传入商品信息，则需在传入 initCustomerServer 对象的参数中，加入 productInfo 参数，调用实例如下</p>
            <div class="code-content-wrap">
        <textarea id="NormalCodeTextarea3" class="code textarea" rows="35">
          <script>
            let option = {
                openUrl: location.origin, // 打开客服聊天框的地址，即：部署后台管理系统的地址，若未填写，则自动获取当前服务器的地址
                insertDomNode: '.getCode_container', // SPA应用必填，html文件单独引入选填，表示插入客服弹窗的 dom节点，一般为当前界面的根节点，默认为body
                token: {{token.token}}, // token,与后台交互的凭证
            isShowTip: true, // 初始化成功后，界面右下角会自动创建 “联系客服按钮”， 如无需默认展示，则填写false即可,默认为true
                // sendUserData为客户信息
                sendUserData: {
                uid: '1', // 用户id
                    nickName: '', // 用户昵称
                    phone: '', // 用户联系方式
                    sex: '1', // 用户性别
                    avatar: '', // 用户头像 URL地址
                    openid: ''
            },
            //
            productInfo: {
                store_name: '蒙奇 D 路飞',
                    stock: '库存',
                    sales: '122', // 销量
                    ficti: '10', // 赠送
                    price: '100',
                    image: 'https://gimg2.baidu.com/image_search/src=http%3A%2F%2Fhbimg.b0.upaiyun.com%2F4495e731345f73cb023b1d70197d50e7f451dbc91a88e-UU7MfN_fw658&refer=http%3A%2F%2Fhbimg.b0.upaiyun.com&app=2002&size=f9999,10000&q=a80&n=0&g=0n&fmt=jpeg?sec=1629276024&t=9d1c5b297dc857ddd2d18c9580dde427'
            }
            };
            var canCustomerServer = new initCustomerServer(option);
            canCustomerServer.init();
          </script>
        </textarea>

            </div>
            <div class="other-wrap">
                <a @click="getCopy('NormalCodeTextarea3')" class="btn btn-blue btn-large" href="javascript:void(0);"><span>复制代码</span></a>
            </div>

            <p class="text-i">如需更新商品信息，则需调用 initCustomerServer 实例化对象的方法 postProductMessage，调用示例如下</p>
            <div class="code-content-wrap">
        <textarea id="NormalCodeTextarea4" class="code textarea" rows="50">
          <script>
            let option = {
                openUrl: location.origin, // 打开客服聊天框的地址，即：部署后台管理系统的地址，若未填写，则自动获取当前服务器的地址
                insertDomNode: '.getCode_container', // SPA应用必填，html文件单独引入选填，表示插入客服弹窗的 dom节点，一般为当前界面的根节点，默认为body
                token: {{token.token}}, // token,与后台交互的凭证
            isShowTip: true, // 初始化成功后，界面右下角会自动创建 “联系客服按钮”， 如无需默认展示，则填写false即可,默认为true
                // sendUserData为客户信息
                sendUserData: {
                uid: '1', // 用户id
                    nickName: '', // 用户昵称
                    phone: '', // 用户联系方式
                    sex: '1', // 用户性别
                    avatar: '', // 用户头像 URL地址
                    openid: ''
            },
            //
            productInfo: {
                store_name: '蒙奇 D 路飞',
                    stock: '库存',
                    sales: '122', // 销量
                    ficti: '10', // 赠送
                    price: '100',
                    image: 'https://gimg2.baidu.com/image_search/src=http%3A%2F%2Fhbimg.b0.upaiyun.com%2F4495e731345f73cb023b1d70197d50e7f451dbc91a88e-UU7MfN_fw658&refer=http%3A%2F%2Fhbimg.b0.upaiyun.com&app=2002&size=f9999,10000&q=a80&n=0&g=0n&fmt=jpeg?sec=1629276024&t=9d1c5b297dc857ddd2d18c9580dde427'
            }
            };
            var canCustomerServer = new initCustomerServer(option);
            canCustomerServer.init();

            // 更新商品信息 示例
            setTimeout(() => {
                canCustomerServer.postProductMessage({
                    store_name: '诺诺罗亚 索隆',
                    stock: '库存',
                    sales: '3447', // 销量
                    ficti: '10', // 赠送
                    price: '654',
                    image: 'https://gimg2.baidu.com/image_search/src=http%3A%2F%2Fww2.sinaimg.cn%2Fmw600%2F81a9359dtw1e2anp2gqtej.jpg&refer=http%3A%2F%2Fwww.sina.com&app=2002&size=f9999,10000&q=a80&n=0&g=0n&fmt=jpeg?sec=1629443073&t=8af76960b38d30d19d50b3991f6bbd01'
                })
            }, 10000)

          </script>
        </textarea>

            </div>
            <div class="other-wrap">
                <a @click="getCopy('NormalCodeTextarea4')" class="btn btn-blue btn-large" href="javascript:void(0);"><span>复制代码</span></a>
            </div>

            <p class="text-i">如果不使用crmChar服务的默认样式，则isShowTip传入为false，打开弹框时使用initCustomerServer 对象的 getCustomeServer 方法</p>
            <div class="code-content-wrap">
        <textarea id="NormalCodeTextarea5" class="code textarea" rows="20">
          <script>
            let option = {
                openUrl: location.origin, // 打开客服聊天框的地址，即：部署后台管理系统的地址，若未填写，则自动获取当前服务器的地址
                insertDomNode: '.getCode_container', // SPA应用必填，html文件单独引入选填，表示插入客服弹窗的 dom节点，一般为当前界面的根节点，默认为body
                token: {{token.token}}, // token,与后台交互的凭证
            isShowTip: true, // 初始化成功后，界面右下角会自动创建 “联系客服按钮”， 如无需默认展示，则填写false即可,默认为true
            };
            var canCustomerServer = new initCustomerServer(option);
            canCustomerServer.init();

            // 调用打开客服弹窗的方法
            canCustomerServer.getCustomeServer();

          </script>
        </textarea>

            </div>
            <div class="other-wrap">
                <a @click="getCopy('NormalCodeTextarea5')" class="btn btn-blue btn-large" href="javascript:void(0);"><span>复制代码</span></a>
            </div>

            <p class="text-i">如若使用a链接直接打开客服弹框，可调用 initCustomerServer 对象的 getOpenUrl 获取打开地址</p>

            <div class="code-content-wrap">
        <textarea id="NormalCodeTextarea6" class="code textarea" rows="20">
          <script>
            let option = {
                openUrl: location.origin, // 打开客服聊天框的地址，即：部署后台管理系统的地址，若未填写，则自动获取当前服务器的地址
                token: {{token.token}}, // token,与后台交互的凭证
            isShowTip: false, // 初始化成功后，界面右下角会自动创建 “联系客服按钮”， 如无需默认展示，则填写false即可,默认为true
            };
            var canCustomerServer = new initCustomerServer(option);
            canCustomerServer.init();

            // 获取界面的Url地址的方法
            var a = document.querySelector(a) // 获取元素
            a.href = canCustomerServer.getOpenUrl(); // 改变元素的href 属性
            a.target="_blank" // 指定打开新界面

          </script>
        </textarea>

            </div>
            <div class="other-wrap">
                <a @click="getCopy('NormalCodeTextarea6')" class="btn btn-blue btn-large" href="javascript:void(0);"><span>复制代码</span></a>
            </div>
            <p class="font-w">参数说明</p>
            <p class="text-i">1. 基本参数</p>
            <Table :columns="columns1" :data="data1"></Table>
            <br>
            <p class="text-i">2. 用户参数：sendUserData</p>
            <Table :columns="columns1" :data="data2"></Table>

            <p class="text-i">3. 产品参数：productInfo</p>
            <Table :columns="columns1" :data="data3"></Table>
            <p class="font-w">应用场景</p>
            <p class="text-i">1. 网站模式模式</p>
            <p class="text-i">2. H5移动端模式</p>
            <p class="text-i">3. a链接模式</p>

            <p class="font-w">3. token获取</p>
            <br>

            <textarea id="NormalCodeTextarea3" class="code textarea" rows="5">
         {{token.token}}
      </textarea>
            <div class="other-wrap">
                <a @click="getCopy('NormalCodeTextarea3')" class="btn btn-blue btn-large" href="javascript:void(0);"><span>复制代码</span></a>
                &nbsp;
                <div @click="resetToken()" class="btn btn-blue btn-large" href="javascript:void(0);"><span>重置token</span></div>

            </div>
            <p style="padding-top:20px;">小贴士：</p>
            <p>1.此代码不会影响您网站页面的显示，请您放心使用。</p>
            <p>2.如果您有技术上的疑问，可咨询您的网站管理员、托管公司、CRMEB。</p>
        </div>

        <Modal v-model="canfrime" title="提示" @on-ok="confirme" @on-cancel="cancel">
            <div class="ivu-modal-confirm">
                <img src="@/assets/images/warring.png" alt="">
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

    import initCustomerServer from '@/libs/customerServer';


    export default {
        name: 'setting_user',
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
                return `${location.origin}/chat/index?token=${this.token.token}&noCanClose=1`;
            }
        },
        data() {
            return {
                canfrime: false,
                srcUrl: `${location.origin}/customerServer.js`,
                cloneTip: false,
                columns1: [
                    {
                        title: '参数',
                        key: 'name'
                    },
                    {
                        title: '类型',
                        key: 'type'
                    },
                    {
                        title: '是否必选',
                        key: 'isRequired'
                    },
                    {
                        title: '示例值',
                        key: 'example'
                    },
                    {
                        title: '参数说明',
                        key: 'message'
                    }
                ],

                data1: [
                    {
                        name: 'openUrl',
                        type: 'String',
                        isRequired: 'true',
                        example: 'http://192.168.31.192:8080',
                        message: '部署后台服务的服务器域名，若不填写，则自动获取放置customerServer.js的服务器域名'
                    },
                    {
                        name: 'domId',
                        type: 'String',
                        isRequired: 'false',
                        example: 'customerServerTip',
                        message: '展示在页面右下角联系客服的dom的id，可根据id获取到dom后自行修改样式, 默认为customerServerTip'
                    },
                    {
                        name: 'token',
                        type: 'String',
                        isRequired: 'true',
                        example: '',
                        message: '交互凭证, 可在该页面下方获取'
                    },
                    {
                        name: 'insertDomNode',
                        type: 'String',
                        isRequired: 'false',
                        example: 'body',
                        message: '被插入的dom类名，SPA应用必传，默认插入至body中'
                    },
                    {
                        name: 'type',
                        type: 'String',
                        isRequired: 'false',
                        example: 'pc',
                        message: '指定引入crmChat服务的端，若不传，customerServer.js 将自行判断当前所在环境'
                    },
                    {
                        name: 'isShowTip',
                        type: 'String',
                        isRequired: 'false',
                        example: 'true | fasle',
                        message: '默认为true，若传入false，则crmChat服务预设的联系客服按钮被隐藏'
                    }
                ],
                data2: [
                    {
                        name: 'uid',
                        type: 'String',
                        isRequired: 'fasle',
                        example: '10',
                        message: '用户的id'
                    },
                    {
                        name: 'nickName',
                        type: 'String',
                        isRequired: 'fasle',
                        example: '张三',
                        message: '用户昵称'
                    },
                    {
                        name: 'phone',
                        type: 'String',
                        isRequired: 'fasle',
                        example: '138****2765',
                        message: '用户联系方式'
                    },
                    {
                        name: 'sex',
                        type: 'String',
                        isRequired: 'true',
                        example: '1',
                        message: '用户性别，0 未知 1男 2女'
                    },
                    {
                        name: 'avatar',
                        type: 'String',
                        isRequired: 'true',
                        example: '1',
                        message: '用户头像url'
                    },
                    {
                        name: 'openid',
                        type: 'String',
                        isRequired: 'true',
                        example: '1',
                        message: '用户的第三方id'
                    }
                ],
                data3: [
                    {
                        name: 'productInfo',
                        type: 'Object',
                        isRequired: 'true',
                        example: '{}',
                        message: '商品详情的对象容器'
                    },
                    {
                        name: 'store_name',
                        type: 'String',
                        isRequired: 'true',
                        example: '碎花裙',
                        message: '商品名称'
                    },

                    {
                        name: 'sales',
                        type: 'Number | String',
                        isRequired: 'true',
                        example: '100',
                        message: '销量'
                    },
                    {
                        name: 'price',
                        type: 'Number | String',
                        isRequired: 'true',
                        example: '10',
                        message: '商品价格'
                    },
                    {
                        name: 'image',
                        type: 'String',
                        isRequired: 'true',
                        example: 'https://gimg2.baidu.com/image_search/src=http%3A%2F%2Fhbimg.b0.upaiyun.com%2F4495e731345f73cb023b1d70197d50e7f451dbc91a88e-UU7MfN_fw658&refer=http%3A%2F%2Fhbimg.b0.upaiyun.com&app=2002&size=f9999,10000&q=a80&n=0&g=0n&fmt=jpeg?sec=1629276024&t=9d1c5b297dc857ddd2d18c9580dde427',
                        message: '商品图片链接'
                    }
                ],
                token: '',
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


                            // js 引入
                            // (function() {
                            //   var hm = document.createElement("script");
                            //   hm.src = "../../../libs/customerServer/customerServer.js";
                            //   var s = document.getElementsByTagName("script")[0];
                            //   s.parentNode.insertBefore(hm, s);
                            // })();
                            // (function() {
                            //   var hm = document.createElement("script");
                            //   hm.src = "http://192.168.31.192:8081/customerServer.js";
                            //   var s = document.getElementsByTagName("script")[0];
                            //   s.parentNode.insertBefore(hm, s);
                            // })();
                            var option = {

                                openUrl: location.origin,
                                type: 'pc', // Mobile
                                // domId: 'customerServerTip',
                                insertDomNode: '.getCode_container',
                                token: this.token.token,
                                isShowTip: true, // true 展示 false 不展示
                                // sendUserData: {
                                // uid: '',
                                //   nickName: '',
                                //   phone: '',
                                //   type: '',
                                //   sex: '1',
                                //   avatar: ''
                                // },
                                // productInfo: {
                                //   store_name: '蒙奇 D 路飞',
                                //   stock: '库存',
                                //   sales: '122', // 销量
                                //   ficti: '10', // 赠送
                                //   price: '100',
                                //   image: 'https://gimg2.baidu.com/image_search/src=http%3A%2F%2Fhbimg.b0.upaiyun.com%2F4495e731345f73cb023b1d70197d50e7f451dbc91a88e-UU7MfN_fw658&refer=http%3A%2F%2Fhbimg.b0.upaiyun.com&app=2002&size=f9999,10000&q=a80&n=0&g=0n&fmt=jpeg?sec=1629276024&t=9d1c5b297dc857ddd2d18c9580dde427'
                                // }
                            }



                            this.canCustomerServer = new initCustomerServer(option);
                            this.canCustomerServer.init();


                        }
                    }
                })
            },
            // 点击测试
            text() {
                console.log(1);
                this.canCustomerServer.getCustomeServer(); // 点击调取客服弹框
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

<style scoped>
    .content {
        width: 100%;
        color: #323437;
        background: #ffffff;
        margin-top: 18px;
        font-size: 13px;
        padding: 10px;
    }
    .table_width {
        padding: 40px;
    }

    .ivu-modal-confirm {
        display: flex;
        align-items: center;
    }
    .ivu-modal-confirm img {
        width: 40px;
        height: 40px;
        margin-right: 30px;
    }

    .font-w {
        font-weight: 800;
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
        margin: 14px 0;
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

    #NormalCodeTextarea {
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
</style>
