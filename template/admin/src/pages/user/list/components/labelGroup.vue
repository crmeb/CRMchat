<template>
  <div class="label-wrapper">
    <div class="label-box" v-for="(item,index) in labelList" :key="index">
      <div class="title">{{item.name}}</div>
      <div class="list">
        <div class="label-item" :class="{'on' : label.disabled == true}" v-for="(label,j) in item.label" :key="j" @click="selectLabel(label)">{{label.label}}</div>
      </div>
    </div>
    <div class="footer">
      <Button type="primary" class="btns" @click="subBtn">确定</Button>
      <Button type="primary" class="btns" ghost @click="cancel">取消</Button>
    </div>
  </div>

  </div>
</template>

<script>
import { getUserLabel, putUserLabel } from '@/api/user'
export default {
  name: "userLabel",
  props: {
    // 用户标签数组，数据结构是两层 [{[]}]
    labelList: {
      type: Array,
      default: () => {
        return []
      }
    },
  },
  data() {
    return {
      activeIds: [],
      unLaberids: []
    }
  },
  watch: {
    uid: {
      handler(nVal, oVal) {
        if(nVal != oVal) {
          this.getList()
        }
      },
      deep: true
    }
  },
  mounted() {
  },
  methods: {
    selectLabel(label) {
      this.$set(label, 'disabled', !label.disabled);
    },
    // 确定
    subBtn() {
      this.$emit('confrim', this.labelList);
    },
    cancel() {
      this.$emit('close')
    }
  }
}
</script>

<style lang="stylus" scoped>
.label-wrapper {
  .list {
    display: flex;
    flex-wrap: wrap;

    .label-item {
      margin: 10px 8px 10px 0;
      padding: 3px 8px;
      background: #EEEEEE;
      color: #333333;
      border-radius: 2px;
      cursor: pointer;
      font-size: 12px;

      &.on {
        color: #fff;
        background: #1890FF;
      }
    }
  }

  .footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 40px;

    button {
      margin-left: 10px;
    }
  }
}

.btn {
  width: 60px;
  height: 24px;
}

.title {
  font-size: 13px;
}
</style>
