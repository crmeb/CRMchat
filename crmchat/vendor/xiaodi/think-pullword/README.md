# think-pullword
ThinkPHP 分词/抽词 扩展包 `5.1` `6.0`

使用梁厂的产品 http://www.pullword.com/

[![Latest Stable Version](https://poser.pugx.org/xiaodi/think-pullword/version)](https://packagist.org/packages/xiaodi/think-pullword)
[![Latest Unstable Version](https://poser.pugx.org/xiaodi/think-pullword/v/unstable)](https://packagist.org/packages/xiaodi/think-pullword)
[![License](https://poser.pugx.org/xiaodi/think-pullword/license)](https://packagist.org/packages/xiaodi/think-pullword)
[![Total Downloads](https://poser.pugx.org/xiaodi/think-pullword/downloads)](https://packagist.org/packages/xiaodi/think-pullword)

## 安装
```
composer require xiaodi/think-pullword
```

## 使用
#### 常规实例化
```php
use PullWord\PullWord;
class Index
{
    public function index()
    {
        $source = '352净水器K10家用直饮大通量1000G 厨下RO反渗透纯水机';
        $pullWord = new PullWord($source);
        $result = $pullWord->pull()->toJson()->get();
        // $result = $pullWord->service('pull', $source)->get();
        // 结果 => string(169) "[{"t":"352净水器"},{"t":"净水器"},{"t":"家用"},{"t":"大通量"},{"t":"1000g"},{"t":"反渗透"},{"t":"反渗透纯水机"},{"t":"渗透"},{"t":"纯水机"}]


        // 商品分类
        // $source = '352净水器K10家用直饮大通量1000G 厨下RO反渗透纯水机';
        // $pullWord = new PullWord($source);
        // $result = $pullWord->classify()->get();
        // // $result = $pullWord->service('classify', $source)->get();
        // 结果 => string(27) "{"class":"电器","idx":11}"
    }
}
```

#### 依赖注入
```php
use PullWord\PullWord;
class Index
{
    public function index(PullWord $pullWord)
    {
        $source = '352净水器K10家用直饮大通量1000G 厨下RO反渗透纯水机';
        $result = $pullWord->pull($source)->get();
        var_dump($result);

        // $source = '352净水器K10家用直饮大通量1000G 厨下RO反渗透纯水机';
        // $result = $pullWord->classify($source)->get();
        // var_dump($result);
    }
}
```

#### 门面
```php
use PullWord\Facade\PullWord;

class Index
{
    public function index()
    {
        $source = '352净水器K10家用直饮大通量1000G 厨下RO反渗透纯水机';
        $result = PullWord::pull($source')->get();
        var_dump($result);
    }
}
```

#### 助手函数
```php
class Index
{
    public function index()
    {
        $source = '352净水器K10家用直饮大通量1000G 厨下RO反渗透纯水机';
        $result = app('pullword')->pull($source)->get();
        var_dump($result);
    }
}

```

### 其它链式方法
#### json返回
```php
$source = '352净水器K10家用直饮大通量1000G 厨下RO反渗透纯水机';
PullWord::pull($source)->toJson()->get();
// 结果 [{"t":"352净水器","p":"1"},{"t":"净水器","p":"1"},{"t":"家用","p":"1"},{"t":"大通量","p":"0.923331"},{"t":"1000g","p":"0.959741"},{"t":"反渗透","p":"0.944082"},{"t":"反渗透纯水机","p":"0.964588"},{"t":"渗透","p":"0.838643"},{"t":"纯水机","p":"0.928798"}]
```
#### 调试模式
结果含有出词概率
```php
$source = '352净水器K10家用直饮大通量1000G 厨下RO反渗透纯水机';
PullWord::pull($source)->debug()->get();
// 结果 [{"t":"352净水器","p":"1"},{"t":"净水器","p":"1"},{"t":"家用","p":"1"},{"t":"大通量","p":"0.923331"},{"t":"1000g","p":"0.959741"},{"t":"反渗透","p":"0.944082"},{"t":"反渗透纯水机","p":"0.964588"},{"t":"渗透","p":"0.838643"},{"t":"纯水机","p":"0.928798"}]
```

#### 设置阈值
出词概率阈值(0-1之间的小数)，1表示只有100%有把握的词才出
```php
$source = '352净水器K10家用直饮大通量1000G 厨下RO反渗透纯水机';
PullWord::pull($source)->threshold(0.4)->toJson()->get();
// 结果 [{"t":"352"},{"t":"352净水器"},{"t":"净水"},{"t":"净水器"},{"t":"家用"},{"t":"大通量"},{"t":"通量"},{"t":"1000g"},{"t":"反渗透"},{"t":"反渗透纯水机"},{"t":"渗透"},{"t":"纯水"},{"t":"纯水机"},{"t":"水机"}]
```
