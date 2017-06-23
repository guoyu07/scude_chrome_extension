
# 重要说明

* 由于拿不到内部真实统计数据, 所以不保证最终结果正确
* 访问次数必须从插件内点击 **前往上课** 才会增加
* 所有数值都是从你第一次使用插件开始累计的
* 时长统计是每分钟统计一次
* 每次统计会获取当前正在浏览的课程页面, 比如你开了多个课程, 则每个课程都会加1分钟
* 同一课程开多个窗口只会增加1分钟

# 安装

前往[下载页面](https://github.com/hteen/scude_chrome_extension/releases)下载最新版插件

1. 打开chrome扩展页面, 地址栏访问 `chrome://extensions/`
2. 拖入插件
3. 确认安装



# 自定义服务器端

服务端使用laravel5.4框架, 数据库文件`sql.sql`包含主要数据表, 因为用到了队列, 请使用以下命令生成队列所需要的数据表

`php artisan queue:table`
`php artisan migrate`

修改`.env` 数据库配置

```linux
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=scude
DB_USERNAME=root
DB_PASSWORD=

QUEUE_DRIVER=database
```
后台队列任务

```linux
php artisan queue:work --tries=3
```


