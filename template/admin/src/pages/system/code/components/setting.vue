<template>
    <div class="content">
        <p class="font-w">使用简介</p>
        <p class="text-i">token重置后，数据将全部更新，历史数据将会失效，请谨慎操作。</p>
        <div class="fenlei">
            <div class="code-content-wrap">
               <textarea id="NormalCodeTextareatoken3" class="code textarea" rows="5">
{{tokeninfo.token_md5}}
      </textarea>
                <div class="other-wrap">
                    <a @click="getCopy('NormalCodeTextareatoken3')" class="btn btn-blue btn-large" href="javascript:void(0);"><span>复制代码</span></a>
                    &nbsp;
                    <div @click="resetToken()" class="btn btn-blue btn-large" href="javascript:void(0);"><span>重置token</span></div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
    export default{
        name: 'wangye',
        props: {
            tokeninfo:{},
            siteUrl:'',
//            cgetCopy:{},
//            cresetToken:{},

        },
        mounted() {

        },
        methods: {
            getCopy(id) {
//                this.cgetCopy(id);
                this.$emit('cgetCopy',id);

            },
            //充值token
            resetToken() {
//                this.cresetToken();
                this.$emit('cresetToken');

            },
        }
    }
</script>

