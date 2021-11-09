<template>
  <div style="margin-top: 20px;">
    <!--统计-->
    <statistics></statistics>
    <!--统计图标-->
    <statisticsCharts></statisticsCharts>
  </div>
</template>

<script>
import statistics from './components/statistics'
import statisticsCharts from "./components/statisticsCharts"
import hotSearch from './hot-search'
import userPreference from './user-preference'
import { getCookies, setCookies } from '@/libs/util'
export default {
  name: 'index',
  components: {
    statistics,
    statisticsCharts,
    hotSearch,
    userPreference
  },
  data() {
    return {
      visitType: 'day', // day, month, year
      visitDate: [(new Date()), (new Date())]
    }
  },
  mounted() {

  },
  methods: {

  }
}
</script>

<style lang="less">
.dashboard-console-visit {
  .ivu-radio-group-button .ivu-radio-wrapper {
    border: none !important;
    box-shadow: none !important;
    padding: 0 12px;
  }
  .ivu-radio-group-button .ivu-radio-wrapper:before,
  .ivu-radio-group-button .ivu-radio-wrapper:after {
    display: none;
  }
}
</style>
