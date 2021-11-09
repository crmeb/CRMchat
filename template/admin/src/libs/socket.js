// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
import { wss } from '@/libs/util';
import Setting from '@/setting';
import Cookies from "js-cookie";
import Vue from 'vue';


let reconneTimer = {};
let reconneCount = {};
let connectGuid = {};

class wsSocket {
    constructor(opt) {
        this.vm = new Vue;
        this.ws = null;
        this.opt = opt || {};
        this.networkStatus = true;
        this.reconneMax = 100;
        this.connectLing = false;
        reconneTimer[this.opt.key] = null;
        reconneCount[this.opt.key] = 0;
        this.init(opt);
        this.networkWath();
        this.defaultEvenv();
    }

    defaultEvenv(){
        this.vm.$on('timeout',this.timeoutEvent.bind(this));
    }
    timeoutEvent(){
        this.reconne();
    }

    guid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0,
                v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    addHandler(element, type, handler) {
        if (element.addEventListener) {
            element.addEventListener(type, handler, false);
        } else if (element.attachEvent) {
            element.attachEvent("on" + type, handler);
        } else {
            element["on" + type] = handler;
        }
    }

    networkWath(){

        this.addHandler(window,'online',()=>{
            this.networkStatus = true;
            console.log('联网了')
            this.ws.close();
            this.vm.$on('timeout',this.timeoutEvent);
        })
        this.addHandler(window,'offline',()=>{
            this.networkStatus = false;
            this.socketStatus = false;
            this.timer && clearInterval(this.timer);
            this.timer = null;
            this.vm.$off('timeout',this.timeoutEvent);
            console.log('断网了')
        });
    }

    reconne(){
        if(reconneTimer[this.opt.key] || this.socketStatus || reconneCount[this.opt.key] > this.reconneMax){
            return;
        }
        reconneTimer[this.opt.key] = setInterval(()=>{
            //断线连接中发现状态为真就不用再连接
            if(this.socketStatus){
                return;
            }
            //正在连接中也不需要在连接了
            if(!this.connectLing){
                console.log('重新连接')
                this.init(this.opt);
                reconneCount[this.opt.key] ++;
            }
        },2000)
    }

    onOpen(key = false) {
        //关闭断线重连定时器
        clearInterval(reconneTimer[this.opt.key]);
        reconneTimer[this.opt.key] = null;

        this.connectLing = false;
        this.opt.open && this.opt.open();
        reconneCount[this.opt.key] = 0
        this.socketStatus = true;
        this.ping();
    }

    init(opt) {
        if(this.socketStatus){
            return;
        }
        let wsUrl = ''
        let hostUrl = wss(Setting.wsSocketUrl);

        hostUrl = hostUrl + '/ws';

        if(opt.key == 1) {
            wsUrl = hostUrl + '?type=admin' + '&token=' + util.cookies.get("token")
        }
        if(opt.key == 2) {
            wsUrl = hostUrl + `?type=kefu` + '&token=' + `${Cookies.get("kefu_token")}`;
        }
        if(opt.key == 3) {
            wsUrl = `${hostUrl}?type=user&form=${opt.form}&token=${opt.token}`;
        }
        if(opt.tourist_uid) {
            wsUrl += '&tourist_uid=' + opt.tourist_uid
        }
        if(wsUrl) {
            this.connectLing = true;
            // connectGuid[opt.key] = this.guid();
            this.ws = new WebSocket(wsUrl);
            this.ws.onopen = this.onOpen.bind(this);
            this.ws.onerror = this.onError.bind(this);
            this.ws.onmessage = this.onMessage.bind(this);
            this.ws.onclose = this.onClose.bind(this);
        }

    }

    ping() {
        var that = this;
        this.timer = setInterval(() => {
            that.send({ type: 'ping' });
        }, 10000);
    }

    send(data) {
        if(!this.socketStatus || this.ws.readyState === 0 || !this.networkStatus){
            this.reconne();
        }
        return new Promise((resolve, reject) => {
            try {
                this.ws.send(JSON.stringify(data));
                resolve({ status: true });
            } catch(e) {
                console.log(e)
                reject({ status: false,socketStatus: this.socketStatus,networkStatus:this.networkStatus})
            }
        });
    }

    onMessage(res) {
        this.opt.message && this.opt.message(res);
    }

    onClose() {
        this.connectLing = false;
        this.timer && clearInterval(this.timer);
        this.timer = null;
        this.opt.close && this.opt.close();
        this.socketStatus = false;
        this.reconne();
    }

    onError(e) {
        this.connectLing = false;
        this.timer && clearInterval(this.timer);
        this.timer = null;
        this.opt.error && this.opt.error(e);
        this.socketStatus = false;
        this.reconne();
    }

    $on(...args) {
        this.vm.$on(...args);
    }
    $off(...args) {
        this.vm.$off(...args);
    }
}

let promises = {};

function createSocket(key, flag, token, tourist_uid, type, form) {
    if(flag) promises[key] = null;
    if(!promises[key])
        promises[key] = new Promise((resolve, reject) => {
            const ws = new wsSocket({
                key,
                token,
                tourist_uid,
                type,
                form,
                open() {
                    resolve(ws);
                },
                error(e) {
                    reject(e)
                },
                message(res) {
                    const { type, data = {} } = JSON.parse(res.data);
                    ws.vm.$emit(type, data);
                },
                close(e) {
                    ws.vm.$emit('close', e);
                }
            })
        });

    return promises[key];
}



export const adminSocket = (flag, token) => createSocket(1, flag, token);
export const Socket = (flag, token, tourist_uid, type) => createSocket(2, flag, token, tourist_uid, type);
export const mobileScoket = (flag, token, form, tourist_uid, type,) => createSocket(3, flag, token, tourist_uid, type, form);
