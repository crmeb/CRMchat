// 图片
var base64ImageObject = {
    // 手机端客服图片
    mobileIcon: 'data:image/png;base64,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',
    // pc端客服图片
    pcIcon: 'data:image/png;base64,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',
    // 关闭客服服务，向下按钮
    putItAway: 'data:image/png;base64,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'
}
var customerServer = null;

//事件添加和执行
window.$chat = {
    event: {},
    on(name, fun) {
        if (this.event[name] == undefined) {
            this.event[name] = [];
        }
        this.event[name].push(fun);
    },
    emit(name, attr) {
        if (this.event[name] && this.event[name].length) {
            this.event[name].map(item => {
                if (typeof item === 'function') {
                    item(...attr)
                }
            })
        }
    }
};

//放入默认事件
window.$chat.on('postMessage', function (type, data) {
    if (!this.iframe_contanier) {
        return;
    }
    this.iframe_contanier.contentWindow.postMessage({type: type, data: data}, "*"); // 传送图文数据
});

const settingObj = {};

//悬浮按钮样式
function customerServerStyle() {

    //PC端悬浮按钮样式
    this.customerServer_container = {
        position: 'fixed',
        bottom: '10px',
        right: '2px',
        // background: 'linear-gradient(270deg, #1890FF 0%, #3875EA 100%)',
        // color: '#fff',
        // 'border-radius': '4px',
        // width: '230px',
        // padding: '8px 10px',
        'box-sizing': 'border-box',
        cursor: 'pointer',
        'z-index': 99
    };
    this.connect_customerServer = {
        display: 'flex',
        'align-items': 'center',
        'justify-content': 'space-between',
    };
    this.connect_customerServer_img = {
        width: '100%',
    };
    //移动端悬浮按钮样式
    this.customerServer_container_mobile = {
        position: 'fixed',
        right: 0,
        top: '500px',
        margin: 'auto',
        width: '40px',
        height: '40px',
        background: 'linear-gradient(270deg, #1890FF 0%, #3875EA 100%)',
        'border-radius': '50%',
        'z-index': 998

    };
    this.customerServer_container_mobile_image = {
        width: '100%',
        height: 'auto',
    };

    //未读消息演示
    this.connent_count = {
        position: 'absolute',
        top: '-12px',
        right: 0,
        background: 'red',
        width: '25px',
        height: '25px',
        'border-radius': '50%',
        display: 'flex',
        'align-items': 'center',
        'justify-content': 'center',
        'font-size': '12px',
        opacity: '.9'
    };
    //iframe样式
    this.iframe_content = {
        position: 'fixed',
        'z-index': 999,
        right: 0,
        'border-radius': '4px',
        transition: '.3s',

    }
}

const customerServerStyleObject = new customerServerStyle();

//初始化函数
function initCustomerServer(option) {
    this.outLine = false; // 是否在离线界面
    this.settingObj = settingObj;
    this.settingObj.openUrl = `${option.openUrl || location.origin}/chat/index`; //服务器地址加路由, 若不传入则自动获取引入应用所在服务器的域名
    this.settingObj.domId = option.customerServerTip || 'customerServerTip'; //浮动客服dom
    this.settingObj.insertDomNode = option.insertDomNode || 'body' // 插入的标签
    this.settingObj.token = option.token; // token为必填项
    this.settingObj.pcIcon = option.pcIcon || base64ImageObject.pcIcon; // pcIcon 电脑端客服图片
    this.settingObj.mobileIcon = option.mobileIcon || base64ImageObject.mobileIcon; // mobile 手机端客服图片
    this.settingObj.deviceType = option.deviceType || ''; // Mobile 手机端打开
    this.settingObj.isShowTip = option.isShowTip; //  false隐藏 true 展示 客服悬浮按钮默认展示
    this.settingObj.windowStyle = option.windowStyle || ''; // pc 端打开默认最精简模式，center居中模式
    this.settingObj.kefuid = option.kefuid || 0; // 指定客服，默认随机
    this.settingObj.sendUserData = option.sendUserData || {}; // 用户信息，默认游客
    this.settingObj.productInfo = option.productInfo || {}; // 携带产品信息，默认空
    this.settingObj.version = option.version || ''//版本号
    this.appDom = null;
    this.initStatus = false;//是否初始化过
    // 判断当前环境下的设备是pc端 || 移动端, 将客户信息挂载到iframe的链接上
    this.setMatchMedia = () => {
        if (!this.settingObj.deviceType) {
            const matchMedia = window.matchMedia;
            // 自动判断启动端 pc 或是 移动
            if (matchMedia('(max-width: 600px)').matches) {
                this.settingObj.deviceType = 'Mobile';
            } else if (matchMedia('(max-width: 992px)').matches) {
                this.settingObj.deviceType = 'pc';
            } else {
                this.settingObj.deviceType = 'pc';
            }
            ;
        }
        // console.log(this.settingObj.deviceType);
        // 获取客服客户相关参数
        let params = {
            token: this.settingObj.token,
            deviceType: this.settingObj.deviceType,
            windowStyle: this.settingObj.windowStyle,
            isShowTip: this.settingObj.isShowTip,
            kefuid: this.settingObj.kefuid
        };
        this.settingObj.openUrl += `?` + toParams(params) + `&`;
        let customerServerData = '';
        if (this.settingObj.sendUserData && Object.keys(this.settingObj.sendUserData).length) {
            customerServerData = toParams(this.settingObj.sendUserData);
            this.settingObj.openUrl += `${customerServerData}`;
        }

        this.settingObj.openUrl += '&version=' + this.settingObj.version
    }


    // 创建 联系客服小弹窗按钮（点击时打开聊天界面）,创建iframe容器 并将iframe添加至body中
    this.createCustomerServerContainer = () => {
        let iframeHtml = `<iframe src="${this.settingObj.openUrl}" frameborder="0" class="iframe_contanier" style="width:100%; height:100%;"></iframe>`;
        var app = document.createElement('div');
        this.appDom = app;
        app.setAttribute('id', 'chat-app');
        if (this.settingObj.deviceType == 'Mobile') {
            // 联系客服按钮dom结构 移动端悬浮按钮样式
            let kefuMobilehtml = `
            <div class="customerServer_container_mobile" id="${this.settingObj.domId}">
              <img class="customerServer_container_mobile_image" src="${this.settingObj.mobileIcon}"></img>
              <div class="connent_count"></div>
            </div>
            `;
            app.innerHTML = kefuMobilehtml;
            this.body = document.querySelector(this.settingObj.insertDomNode);
            this.body.appendChild(app);


            var fwuss = document.querySelector('.customerServer_container_mobile');
            var maxW = document.body.clientWidth - 50;
            var maxH = document.body.clientHeight - 50;

            var oL, oT;
            fwuss.addEventListener('touchstart', (e) => {

                var ev = e || window.event;
                var touch = ev.targetTouches[0];
                oL = touch.clientX - fwuss.offsetLeft;
                oT = touch.clientY - fwuss.offsetTop;

                document.addEventListener("touchmove", defaultEvent, false);
            })
            fwuss.addEventListener('touchmove', (e) => {
                var ev = e || window.event;
                var touch = ev.targetTouches[0];
                var oLeft = touch.clientX - oL;
                var oTop = touch.clientY - oT;
                if (oLeft < 0) {
                    oLeft = 0;
                } else if (oLeft >= maxW) {
                    oLeft = maxW;
                }
                if (oTop < 0) {
                    oTop = 0;
                } else if (oTop >= maxH) {
                    oTop = maxH;
                }
                fwuss.style.left = oLeft + 'px';
                fwuss.style.top = oTop + 'px';
            });

            fwuss.addEventListener('touchend', function () {
                document.removeEventListener("touchmove", defaultEvent);
            });

            function defaultEvent(e) {
                e.preventDefault();
            }


        } else {
            //电脑端悬浮按钮样式
            let kefuhtml = `
            <div class="customerServer_container" id="${this.settingObj.domId}">
              <div class="connect_customerServer">
                  <img class="connect_customerServer_img" src="${this.settingObj.pcIcon}"></img>
              </div>
              <div class="connent_count"></div>
            </div>
            `;
            app.innerHTML = kefuhtml;
            this.body = document.querySelector(this.settingObj.insertDomNode);
            this.body.appendChild(app);

        }


        // 创建完毕后，添加样式，样式可以从外部传入
        this.iframeLayout = document.createElement('div');
        this.iframeLayout.setAttribute('id', 'iframe_content');
        this.setStyleOfCustomerServer(this.iframeLayout, customerServerStyleObject.iframe_content);
        this.iframeLayout.style['z-index'] = 999;
        this.iframeLayout.innerHTML = iframeHtml;
        this.body.appendChild(this.iframeLayout);


        // 获取联系客服按钮dom对象
        this.connentServerDom = document.querySelector(`#${this.settingObj.domId}`);

        // 判断联系客服按钮是否默认展示
        if (this.settingObj.isShowTip === false) {
            this.connentServerDom.style.display = 'none';
        }
        // 获取 iframe 弹框dom对象，便于后期数据交互
        this.iframe_contanier = document.querySelector('.iframe_contanier');

    }

    // 设置基本样式样式
    this.batchSetStyle = () => {
        Object.keys(customerServerStyleObject).forEach(item => {
            if (document.querySelector(`.${item}`)) {
                this.setStyleOfCustomerServer(document.querySelector(`.${item}`), customerServerStyleObject[item]);
            }
        })
    }

    // 设置初始化样式，包括iframe弹宽初始定位，未读消息等
    this.initPositionStyle = () => {
        //移动端初始化样式
        let mobileInitStyle = {
            width: '100%',
            height: '100%',
            top: '100%',
            left: 0
        }
        // pc端初始化样式
        let pcInitStyle = {
            width: '377px',
            bottom: '-645px',
            height: '645px',
            'z-index': 999,
            'box-shadow': '1px 1px 15px 0px rgba(0, 0, 0, 0.3)'

        }
        // 判断设备的类型，是移动端或是pc端
        if (this.settingObj.deviceType == 'Mobile') {
            this.setStyleOfCustomerServer(this.iframeLayout, mobileInitStyle);
        } else {
            this.setStyleOfCustomerServer(this.iframeLayout, pcInitStyle);
        }

        // 用来展示未读消息数的小圆点
        this.connent_count = document.querySelector('.connent_count');
        this.connent_count.style.display = 'none';
    }
    //加载聊天框
    this.loadwindow = () => {
        // 接收来自iframe中的参数
        window.addEventListener("message", e => {
            window.$chat.emit(e.data.type, [e.data]);
            // 关闭弹框
            if (e.data.type == 'closeWindow') {
                if (this.settingObj.deviceType == 'Mobile') {
                    this.iframeLayout.style.top = '100%';
                } else if (this.settingObj.windowStyle == 'center') {
                    this.setStyleOfCustomerServer(this.iframeLayout, {
                        display: 'none'
                    });

                } else {
                    this.iframeLayout.style.bottom = '-660px';
                    this.iframeLayout.style.opacity = '0';

                }
                if (this.settingObj.isShowTip !== false) {
                    this.connentServerDom.style.display = 'block';
                }

            }

            // 收取未读消息
            if (e.data.type == 'onMessageNum') {

                if (e.data.data.num > 0) {
                    this.connent_count.style.display = 'flex';
                    this.connent_count.innerHTML = e.data.data.num;
                } else {
                    this.connent_count.style.display = 'none';
                }
            }

            // 跳转到离线留言界面
            if (e.data.type == 'customerOutLine') {
                this.outLine = true;
                this.setStyleOfCustomerServer(this.iframeLayout, {
                    width: this.outLine ? '378px' : '730px',
                })
            }
            // 监听，跳转回中间页，重置outline（来自反馈成功界面）
            if (e.data.type == 'reload') {
                this.outLine = false;

            }

        });

    };
    // 打开客服聊天框
    this.getCustomeServer = () => {

        //检测是否初始化过
        if (this.initStatus === false) {
            this.init();
        }

        if (this.settingObj.deviceType == 'Mobile') {
            this.iframeLayout.style.top = '0';
        } else if (this.settingObj.windowStyle == 'center') {
            this.setStyleOfCustomerServer(this.iframeLayout, {
                top: 0,
                left: 0,
                bottom: 0,
                right: 0,
                margin: 'auto',
                width: this.outLine ? '378px' : '730px',
                display: 'block',
                transition: 'none',
                'border-radius': '8px',
                overflow: 'hidden',
                'box-shadow': '1px 1px 15px 0px rgba(0, 0, 0, 0.3)'
            });

        } else {
            this.iframeLayout.style.bottom = 0;
            this.iframeLayout.style.opacity = '1';

        }
        //悬浮按钮隐藏
        this.connentServerDom.style.display = 'none';
        this.iframe_contanier.contentWindow.postMessage({
            type: 'getImgOrText',
            productInfo: this.settingObj.productInfo
        }, "*"); // 传送图文数据
        this.iframe_contanier.contentWindow.postMessage({type: 'openCustomeServer'}, "*"); //通知iframe 打开了客服弹框
        //打开聊天窗事件
        window.$chat.emit('openChatWin');
    }

    // 更新传送的图文信息
    this.postProductMessage = (productInfo) => {
        this.iframe_contanier.contentWindow.postMessage({type: 'getImgOrText', productInfo: productInfo}, "*"); // 传送图文数据
    }


}

//销毁事件
initCustomerServer.prototype.destroy = function () {
    this.appDom.remove()
    this.iframeLayout.remove();
    this.initStatus = false;
}

//初始化
initCustomerServer.prototype.init = function () {
    this.setMatchMedia();
    this.createCustomerServerContainer();
    this.batchSetStyle();
    this.initPositionStyle();
    this.loadwindow();
    this.initStatus = true;
    this.connentServerDom.removeEventListener('click', this.getCustomeServer);
    // 联系客服小按钮，点击事件
    this.connentServerDom.addEventListener('click', this.getCustomeServer)
    //初始化事件
    window.$chat.emit('init');
};
//封装全局设置样式方法
initCustomerServer.prototype.setStyleOfCustomerServer = function (dom, styleObj) {
    Object.keys(styleObj).forEach(item => {
        dom['style'][item] = styleObj[item]
    })
};
//封装全局获取openUle方法
initCustomerServer.prototype.getOpenUrl = function () {
    return this.settingObj.openUrl;
}

window.getChatInstance = function () {
    return customerServer;
}

//vue 开发调试专用，vue开发请去掉下一行注释
// export default initCustomerServer;
window.onload = function () {
    var chatJava = document.getElementById('chat');
    if (chatJava) {
        var option = JSON.parse(chatJava.getAttribute('option') || '');
        if (option.authInit) {
            customerServer = new initCustomerServer(option);
            customerServer.init();

            window.$chat.emit('chatAuthAfter', [customerServer]);
        }
    }
}

// let useCustomerServer = new initCustomerServer(option);
// useCustomerServer.init();

// 生成指定范围内的随机数
function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function ajax(options) {
    var xhr = null;
    var params = options.data;
    //创建对象
    if (window.XMLHttpRequest) {
        xhr = new XMLHttpRequest()
    } else {
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    }

    switch (options.type) {
        case 'GET':
            xhr.open(options.type, options.url + "?" + params, options.async);
            xhr.send(null);
            break;
        case 'POST':
            xhr.open(options.type, options.url, options.async);
            xhr.setRequestHeader("Content-Type", "application/json; charset=utf-8");
            xhr.setRequestHeader("Authori-zation", `Bearer ${token}`);
            xhr.send(JSON.stringify(params));
            break;
        default:
            break;
    }

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            options.success(xhr.responseText);
        }
    }
}


// 将Object 改装成以 & 符号连接的字符串
function toParams(param) {
    var result = ""
    for (let name in param) {
        if (typeof param[name] != 'function') {
            result += "&" + name + "=" + encodeURI(param[name]);
        }
    }

    return result.substring(1)
}

//set session
function setSen(k, val) {
    if (typeof val == 'string') {
        sessionStorage.setItem(k, val);
        return val;
    }
    sessionStorage.setItem(k, JSON.stringify(val));
    return val;
}

//get session
function getSen(k) {
    let uu = sessionStorage.getItem(k);

    try {
        if (typeof JSON.parse(uu) != 'number') {
            uu = JSON.parse(uu);
        }
    } catch (e) {
    }
    return uu;
}

//set local
function setLoc(k, val) {
    if (typeof val == 'string') {
        localStorage.setItem(k, val);
        return val;
    }
    localStorage.setItem(k, JSON.stringify(val));
    return val;
}

//get local
function getLoc(k) {
    let uu = localStorage.getItem(k);

    try {
        if (typeof JSON.parse(uu) != 'number') {
            uu = JSON.parse(uu);
        }
    } catch (e) {
    }
    return uu;
}

//序列化对象和数组
function serialize(data) {
    if (data != null && data != '') {
        try {
            return JSON.parse(JSON.stringify(data));
        } catch (e) {
            if (data instanceof Array) {
                return [];
            }
            return {};
        }
    }
    return data;
}


