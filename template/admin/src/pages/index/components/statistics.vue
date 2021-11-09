<template>
  <Row :gutter="24">
    <Col class="ivu-mb" span="6">
    <Card :bordered="false" dis-hover :padding="12">
      <p slot="title">
        <span>全部客户</span>
      </p>
      <Tag slot="extra" color="green">全部</Tag>
      <div>
        <div class="number">{{statisticsList.all}}</div>
        <Divider style="margin: 8px 0" />
        <div class="ivu-pt-8" style="height: 22px;">
          累计客户数量<span class="renshu">{{statisticsList.all}}</span>人
        </div>
      </div>
    </Card>
    </Col>
    <Col class="ivu-mb" span="6">
    <Card :bordered="false" dis-hover :padding="12">
      <p slot="title">
        <span>新增客户</span>
      </p>
      <Tag slot="extra" color="green">今日</Tag>
      <div>
        <div class="number">{{statisticsList.toDayKefu}}</div>
        <Divider style="margin: 8px 0" />
        <div class="ivu-pt-8" style="height: 22px;">
          今日新增客户<span class="renshu">{{statisticsList.toDayKefu}}</span>人
        </div>

      </div>
    </Card>
    </Col>
    <Col class="ivu-mb" span="6">
    <Card :bordered="false" dis-hover :padding="12">
      <p slot="title">
        <span>新增游客</span>
      </p>
      <Tag slot="extra" color="green">今日</Tag>
      <div>
        <div class="number">{{statisticsList.toDayTourist}}</div>
        <Divider style="margin: 8px 0" />
        <div class="ivu-pt-8" style="height: 22px;">
          今日新增游客<span class="renshu">{{statisticsList.toDayTourist}}</span>人
        </div>

      </div>
    </Card>
    </Col>
    <Col class="ivu-mb" span="6">
    <Card :bordered="false" dis-hover :padding="12">
      <p slot="title">
        <span>新增客户</span>
      </p>
      <Tag slot="extra" color="green">本月</Tag>
      <div>
        <div class="number">{{statisticsList.month}}</div>
        <Divider style="margin: 8px 0" />
        <div class="ivu-pt-8" style="height: 22px;">
          本月新增客户<span class="renshu">{{statisticsList.month}}</span>人
        </div>

      </div>
    </Card>
    </Col>
  </Row>
  <!--<div class="statistics">-->
    <!--<ul class="statistics-ul">-->
      <!--<li>-->
        <!--<div class="text">全部客户</div>-->
        <!--<div class="number-li">-->
          <!--<countTo :startVal='0' :endVal='statisticsList.all' :duration='durations'></countTo>-->
          <!--<span>人</span>-->
        <!--</div>-->
      <!--</li>-->
      <!--<li>-->
        <!--<div class="text">今日新增客户</div>-->
        <!--<div class="number-li">-->
          <!--<countTo :startVal='0' :endVal='statisticsList.toDayKefu' :duration='durations'></countTo>-->
          <!--<span>人</span>-->
        <!--</div>-->
      <!--</li>-->
      <!--<li>-->
        <!--<div class="text">本月新增客户</div>-->
        <!--<div class="number-li">-->
          <!--<countTo :startVal='0' :endVal='statisticsList.month' :duration='durations'></countTo>-->
          <!--<span>人</span>-->
        <!--</div>-->
      <!--</li>-->
      <!--<li>-->
        <!--<div class="text">今日游客</div>-->
        <!--<div class="number-li">-->
          <!--<countTo :startVal='0' :endVal='statisticsList.toDayTourist' :duration='durations'></countTo>-->
          <!--<span>人</span>-->
        <!--</div>-->
      <!--</li>-->
    <!--</ul>-->
  <!--</div>-->
</template>

<script>
import { sumApi } from "@/api/index";
import countTo from 'vue-count-to';
export default {
  name: "statistics",
  components: { countTo },
  data() {
    return {
      durations: 3000,
      statisticsList: {}
    }
  },
  methods: {
    getStatistics() {
      sumApi().then(async res => {
        let da = res.data
        this.statisticsList = da;
        console.log(da);
      }).catch(res => {
        this.$Message.error(res.msg)
      })
    }
  },
  mounted() {
    this.getStatistics()
  }
}
</script>

<style lang="less">
  .number{
    font-size: 30px;
    margin-bottom: 10px;
  }
  .renshu{
    color: red;
    padding: 0 3px;
  }
.statistics {
  width: 100%;
  margin: 20px 0;
  .statistics-ul {
    width: 100%;
    list-style: none;
    display: flex;
    li {
      width: 24%;
      list-style: none;
      padding: 28px 8px;
      margin-right: 2%;
      background-color: #ffffff;
      .text {
        color: #777777;
        font-size: 14px;
        padding-bottom: 6px;
      }
      .number-li {
        color: #282828;
        font-size: 19px;
        font-weight: 600;
        span {
          font-size: 13px;
        }
        span:first-of-type {
          font-size: 19px;
        }
      }
    }
    li:last-of-type {
      margin-right: 0;
    }
  }
}
</style>
