<template>
  <div class="label-wrapper">
    <div class="label-box">
      <div class="title"></div>
      <div class="list">
        <div class="label-item" :class="{ 'on': selectGroup.id? selectGroup.id == item.id : activeUserInfo.group_id == item.id   }" v-for="(item,index) in userGroup" :key="index" @click="selectLabel(item)">{{item.group_name}}</div>
      </div>
    </div>
    <div class="footer">
      <Button type="primary" class="btns" @click="subBtn">确定</Button>
      <Button type="primary" class="btns" ghost @click="cancel">取消</Button>
    </div>
  </div>
</template>

<script>
import { userGroupApi } from '@/api/kefu';
export default {
  name: "userLabel",
  props: {
    activeUserInfo: {
      type: Object,
      default: () => {
        return {}
      }
    },
    userGroup: {
      type: Array,
      default: () => {
        return []
      }
    }
  },
  data() {
    return {
      selectGroup: {}

    }
  },
  watch: {
  },
  mounted() {
  },
  methods: {
    selectLabel(item) {
      this.selectGroup = item;
      // this.$emit('selectGroup', item);
    },
    // 确定
    subBtn() {
      this.$emit('handleSelectGroup', this.selectGroup);
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
