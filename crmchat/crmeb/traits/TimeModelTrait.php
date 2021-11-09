<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2020 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace crmeb\traits;


use Carbon\Carbon;
use think\Model;

trait TimeModelTrait
{

    /**
     * 时间查询字段名
     * @var null
     */
    protected $timeField = 'create_time';

    /**
     * 设置时间查询字段
     * @param string $timeField
     * @return $this
     */
    public function setTimeField(string $timeField)
    {
        $this->timeField = $timeField;
        return $this;
    }

    /**
     * 获取时间查询字段
     * @return string|null
     */
    public function getTimeField(): string
    {
        return $this->timeField;
    }

    /**
     * 时间查询作用域
     * @param Model $query
     * @param $value
     * @return mixed
     */
    public function searchTimeAttr($query, $value)
    {
        $createTimeField = $this->getTimeField();
        switch ($value) {
            case 'today'://今天
                $query->whereBetween($createTimeField, [Carbon::today()->startOfDay()->toDateTimeString(), Carbon::today()->endOfDay()->toDateTimeString()]);
                break;
            case 'week'://本周
                $query->whereBetween($createTimeField, [Carbon::today()->startOfWeek()->toDateTimeString(), Carbon::today()->endOfWeek()->toDateTimeString()]);
                break;
            case 'month'://本月
                $query->whereBetween($createTimeField, [Carbon::today()->startOfMonth()->toDateTimeString(), Carbon::today()->endOfMonth()->toDateTimeString()]);
                break;
            case 'year'://今年
                $query->whereBetween($createTimeField, [Carbon::today()->startOfYear()->toDateTimeString(), Carbon::today()->endOfYear()->toDateTimeString()]);
                break;
            case 'yesterday'://昨天
                $query->whereBetween($createTimeField, [date('Y-m-d H:i:s', strtotime('yesterday')), date('Y-m-d H:i:s', strtotime('today -1second'))]);
                break;
            case 'last year'://去年
                $query->whereBetween($createTimeField, [Carbon::today()->subYear()->year, Carbon::today()->subYear()->endOfYear()->toDateTimeString()]);
                break;
            case 'last week'://上周
                $query->whereBetween($createTimeField, [Carbon::today()->subWeek()->startOfWeek()->toDateTimeString(), Carbon::today()->subWeek()->endOfWeek()->toDateTimeString()]);
                break;
            case 'last month'://上个月
                $query->whereBetween($createTimeField, [Carbon::today()->subMonth()->startOfMonth()->toDateTimeString(), Carbon::today()->subMonth()->endOfMonth()->toDateTimeString()]);
                break;
            case 'quarter'://本季度
                $query->whereBetween($createTimeField, [Carbon::today()->startOfQuarter()->toDateTimeString(), Carbon::today()->endOfQuarter()->toDateTimeString()]);
                break;
            case 'lately7'://近7天
                $query->whereBetween($createTimeField, [Carbon::today()->subDays(7)->toDateTimeString(), Carbon::today()->toDateTimeString()]);
                break;
            case 'lately30':
                $query->whereBetween($createTimeField, [Carbon::today()->subDays(30)->toDateTimeString(), Carbon::today()->toDateTimeString()]);
                break;
            default:
                if (false !== strstr($value, '-')) {
                    [$startTime, $endTime] = explode('-', $value);
                    $startTime = str_replace('/', '-', trim($startTime));
                    $endTime   = str_replace('/', '-', trim($endTime));
                    if ($startTime && $endTime && $startTime != $endTime) {
                        $query->whereBetween($createTimeField, [$startTime, $endTime]);
                    } else if ($startTime && $endTime && $startTime == $endTime) {
                        $query->whereBetween($createTimeField, [$startTime, date('Y-m-d H:i:s', strtotime($endTime) + 86400)]);
                    } else if (!$startTime && $endTime) {
                        $query->whereTime($createTimeField, '<', $endTime);
                    } else if ($startTime && !$endTime) {
                        $query->whereTime($createTimeField, '>=', $startTime);
                    }
                } elseif (preg_match('/^lately+[1-9]{1,3}/', $value)) {
                    //最近天数 lately[1-9] 任意天数
                    $day = (int)str_replace('lately', '', $value);
                    if ($day) {
                        $query->whereBetween($createTimeField, [Carbon::today()->subDays($day)->toDateTimeString(), Carbon::today()->toDateTimeString()]);
                    }
                }
        }
    }
}
