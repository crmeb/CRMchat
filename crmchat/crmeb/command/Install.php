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

namespace crmeb\command;

use app\services\system\admin\SystemAdminServices;
use app\services\system\config\SystemConfigServices;
use crmeb\services\MysqlBackupService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\console\Table;
use think\db\exception\BindParamException;

class Install extends Command
{

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $isDemo = 1;

    /**
     * 不会被清除掉的表
     * @var array
     */
    protected $unCleanTable = [
        'eb_system_admin',
        'eb_system_config',
        'eb_system_config_tab',
        'eb_system_menus',
        'eb_system_group',
        'eb_system_group_data',
        'eb_chat_service_speechcraft',
        'eb_cache'
    ];

    /**
     * 所需扩展
     * @var string[]
     */
    protected $checkLoaded = [
        'redis', 'swoole', 'fileinfo'
    ];

    /**
     * 函数检测
     * @var string[]
     */
    protected $checkFunction = [
        'curl_init', 'openssl_encrypt', 'gd_info'
    ];

    /**
     *
     */
    protected function configure()
    {
        $this->setName('install')
            ->addArgument('status', Argument::OPTIONAL, 'start/remove')
            ->addOption('h', null, Option::VALUE_OPTIONAL, '网站域名')
            ->addOption('db', null, Option::VALUE_OPTIONAL, '是否卸载数据库', 0)
            ->addOption('demo', null, Option::VALUE_OPTIONAL, '是否保留默认数据', 1)
            ->setDescription('命令行一键安装/卸载');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @throws BindParamException
     */
    protected function execute(Input $input, Output $output)
    {
        $status = $input->getArgument('status') ?: 'start';

        $this->isDemo = $input->getOption('demo');

        $database = env('database.database');
        if (in_array($status, ['start', 'remove'])) {
            if (!env('database.hostname') ||
                !$database ||
                !env('database.username') ||
                !env('database.password') ||
                !env('database.hostport')
            ) {
                $output->error('请先配置数据库,确保数据库能正常连接!');
                return;
            }
        }

        if ('7.1' > phpversion()) {
            throw new \Exception('您的php版本过低，不能安装本软件，兼容php版本7.1~7.4，谢谢！');
        }

        if (phpversion() > '7.5') {
            throw new \Exception('您的php版本太高，不能安装本软件，兼容php版本7.1~7.4，谢谢！');
        }

        if ($input->hasOption('h')) {
            $this->host = $input->getOption('h');
        }
        if (!$status || !in_array($status, ['start', 'remove'])) {
            $question = $output->choice($input, ' 请选择执行指令数字 ', ['start' => '1', 'remove' => '-1', 'exit' => 0]);
            if ($question === 'exit') {
                $output->info('您已退出命令');
                return;
            } else {
                $status = $question;
            }
        }
        if ($status === 'remove') {
            $this->remove($input, $output);
        } elseif ($status === 'start') {
            $this->start($input, $output);
        }
        return;
    }

    /**
     * 开始安装
     * @param Input $input
     * @param Output $output
     * @throws BindParamException
     */
    protected function start(Input $input, Output $output)
    {
        $installLockDir = root_path('public') . 'install/install.lock';
        if (file_exists($installLockDir)) {
            $crmeb    = get_crmeb_version();
            $question = $output->confirm($input, '您已经安装' . $crmeb . '版本是否重新安装,重新安装会清除掉之前的数据请谨慎操作?', false);
            if ($question) {
                $res = $this->authBackups();
                if (!$res) {
                    $output->info('已退出安装程序');
                }
                $database = env('database.database');
                $this->dropTable($database);

                unlink($installLockDir);
            } else {
                $output->info('已退出安装程序');
                return;
            }
        }
        //环境检测
        $this->environmentDetection();

        $installSql = file_get_contents(root_path('public' . DIRECTORY_SEPARATOR . 'install') . 'crmeb.sql');
        if (!$installSql) {
            $output->error('读取安装sql失败,请检查安装sql文件权限,再次尝试安装!');
            return;
        }

        $this->query($installSql);

        $this->cleanTable();

        $this->output->writeln('+---------------------------- [创建管理员] ---------------------------------+');
        $this->output->newLine();
        [$account, $password] = $this->createAdmin();

        file_put_contents($installLockDir, time());
        chown($installLockDir, 'www');

        $output->info('安装完成!!请妥善保管您的账号密码!');
        $output->info('账号:' . $account);
        $output->info('密码:' . $password);

        return;
    }

    /**
     * 环境检测
     */
    protected function environmentDetection()
    {
        $table = new Table();
        $this->output->writeln('+----------------------------- [环境检测] -----------------------------------+');
        $this->output->newLine();
        $header = ['扩展名', '是否安装'];
        $table->setHeader($header);
        $load = true;
        foreach ($this->checkLoaded as $loaded) {
            if (extension_loaded($loaded)) {
                $table->addRow([$loaded, '已安装']);
            } else {
                $load = false;
                $table->addRow([$loaded, '未安装']);
            }
        }
        $header = ['函数名', '是否存在'];
        $table->setHeader($header);
        foreach ($this->checkFunction as $fun) {
            if (function_exists($fun)) {
                $table->addRow([$fun, '已存在']);
            } else {
                $load = false;
                $table->addRow([$fun, '不存在']);
            }
        }

        $this->output->writeln($table->render());
        $this->output->newLine(2);

        if ($load == false) {
            throw new \Exception('请安装必要扩展');
        }
    }

    /**
     * 创建账号
     */
    protected function createAdmin()
    {
        $account  = $this->adminAccount();
        $password = $this->adminPassword();
        /** @var SystemAdminServices $service */
        $service  = app()->make(SystemAdminServices::class);
        $tablepre = env('database.prefix');
        $this->app->db->query('truncate table ' . $tablepre . 'system_admin');
        try {
            $service->create(['conf_pwd' => $password, 'roles' => [1], 'pwd' => $password, 'account' => $account, 'level' => 0, 'status' => 1]);
        } catch (\Throwable $e) {
            $this->output->writeln($e->getMessage());
            $this->output->newLine();
            $this->createAdmin();
        }

        if ($this->host) {
            /** @var SystemConfigServices $configService */
            $configService = app()->make(SystemConfigServices::class);
            $configService->update('site_url', ['value' => json_encode($this->host)], 'menu_name');
        }

        return [$account, $password];
    }

    /**
     * 卸载程序
     * @param Input $input
     * @param Output $output
     * @throws BindParamException
     */
    protected function remove(Input $input, Output $output)
    {
        $installLockDir = root_path('public') . 'install/install.lock';
        if (!file_exists($installLockDir)) {
            $this->output->info('你尚未安装本程序');
            return;
        }
        $database = env('database.database');
        $output->info(' 正在进行卸载中...');

        $this->authBackups();

        if ($input->hasOption('db')) {
            $question = $output->confirm($input, '您确定要清除掉[' . $database . ']数据吗?', false);
            if ($question) {
                $this->dropTable($database);
            }
        }

        unlink($installLockDir);

        $this->output->info('卸载完成');
        return;
    }

    /**
     * 清除所有表数据
     * @param string $database
     */
    protected function dropTable(string $database)
    {
        $this->output->writeln('+---------------------------- [清理表数据] ---------------------------------+');
        $this->output->newLine();
        $this->output->write("\r 正在清理表数据");

        /** @var MysqlBackupService $service */
        $service   = app()->make(MysqlBackupService::class, [[
            //数据库备份卷大小
            'compress' => 1,
            //数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level'    => 5,
        ]]);
        $dataList  = $service->dataList();
        $tableName = array_column($dataList, 'name');
        $count     = count($tableName);
        if ($count) {
            $res = $this->app->db->transaction(function () use ($database, $tableName) {
                foreach ($tableName as $name) {
                    $this->app->db->query('DROP TABLE ' . $name);
                }
            });
        }

        $this->output->write("\r 已清理完毕");
        $this->output->newLine(2);

        return $res;
    }

    /**
     * 执行安装sql
     * @param string $installSql
     */
    protected function query(string $installSql)
    {
        $tablepre = env('database.prefix', '');
        $sqlArray = $this->sqlSplit($installSql, $tablepre);
        $dataname = env('database.database');
        try {
            $res = $this->app->db->query('select * from information_schema.SCHEMATA where SCHEMA_NAME = "' . $dataname . '"');
            if (!$res) {
                $this->app->db->query('CREATE DATABASE ' . $dataname);
            }
        } catch (\Throwable $e) {
            throw $e;
        }
        $table = new Table();
        $this->output->writeln('+----------------------------- [SQL安装] -----------------------------------+');
        $this->output->newLine();
        $header = ['表名', '执行结果', '错误原因', '时间'];
        $table->setHeader($header);
        $tableNameData = [];
        foreach ($sqlArray as $sql) {
            $sql = trim($sql);
            if (strstr($sql, 'CREATE TABLE')) {
                preg_match('/CREATE TABLE (IF NOT EXISTS)? `eb_([^ ]*)`/is', $sql, $matches);
                $tableName       = $tablepre . ($matches[2] ?? '');
                $tableNameData[] = $tableName;
            } else {
                $tableName = '';
            }
            try {
                $this->app->db->transaction(function () use ($tablepre, $sql, $tableName) {

                    $sql = str_replace('`eb_', '`' . $tablepre, $sql);//替换表前缀
                    $this->app->db->query($sql);

                });

                $tableName && $table->addRow([$tableName, 'ok', '无错误', date('Y-m-d H:i:s')]);

            } catch (\Throwable $e) {
                $tableName && $table->addRow([$tableName, 'x', $e->getMessage(), date('Y-m-d H:i:s')]);
            }
        }

        if (!$this->isDemo) {
            $unCleanTable = $this->unCleanTable;
            foreach ($unCleanTable as $k => $v) {
                $unCleanTable[$k] = str_replace('eb_', $tablepre, $v);
            }
            foreach ($tableNameData as $table) {
                if (!is_array($table, $unCleanTable)) {
                    $this->app->db->transaction(function () use ($table) {
                        $this->app->db->query('truncate table ' . $table);
                    });
                }
            }
        }

        $this->output->writeln($table->render());
        $this->output->newLine(2);
    }

    /**
     * 账号
     * @return bool|mixed|string
     */
    protected function adminAccount()
    {
        $account = $this->output->ask($this->input, '请输入后台登陆账号,最少4个字符', null, function ($value) {
            if (strlen($value) < 4) {
                return false;
            }
            return $value;
        });
        if (!$account) {
            $this->output->error('账号至少4个字符');
            $this->output->newLine();
            $account = $this->adminAccount();
        }
        return $account;
    }

    /**
     * 密码
     * @return bool|mixed|string|null
     */
    protected function adminPassword()
    {
        $password = $this->output->ask($this->input, '请输入登陆密码,密码为数字加字母/字母加符号/数字加字符的组合不能少于6位');
        if (!preg_match('/^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^<>&*]+$)[a-zA-Z\d!@#$%^&<>*]{6,16}$/', $password)) {
            $this->output->error('请输入符合要求的密码');
            $this->output->newLine();
            $password = $this->adminPassword();
        }
        return $password;
    }

    /**
     * 清楚多余数据
     */
    protected function cleanTable()
    {
        $tablepre = env('database.prefix');
        $database = env('database.database');
        $blTable  = ['eb_system_admin', 'eb_system_role', 'eb_system_config',
            'eb_system_config_tab', 'eb_system_menus', 'eb_system_group',
            'eb_system_group_data', 'eb_agreement', 'eb_chat_service_speechcraft',
            'eb_cache'];
        if ($tablepre !== 'eb_') {
            $blTable = array_map(function ($name) use ($tablepre) {
                return str_replace('eb_', $tablepre, $name);
            }, $blTable);
        }
        $tableList = $this->app->db->query("select table_name from information_schema.tables where table_schema='$database'");
        $tableList = array_column($tableList, 'table_name');
        foreach ($tableList as $table) {
            if (!in_array($table, $blTable)) {
                $this->app->db->query('truncate table ' . $table);
            }
        }
    }

    /**
     * 切割sql
     * @param $sql
     * @return array
     */
    protected function sqlSplit(string $sql, string $tablepre)
    {
        if ($tablepre != "tp_")
            $sql = str_replace("tp_", $tablepre, $sql);

        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);

        $sql          = str_replace("\r", "\n", $sql);
        $ret          = [];
        $num          = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries   = explode("\n", trim($query));
            $queries   = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            $num++;
        }
        return $ret;
    }

    /**
     * 自动备份表
     * @return bool|mixed|string|null
     * @throws BindParamException
     */
    protected function authBackups(bool $g = false)
    {
        /** @var MysqlBackupService $service */
        $service   = app()->make(MysqlBackupService::class, [[
            //数据库备份卷大小
            'compress' => 1,
            //数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level'    => 5,
        ]]);
        $dataList  = $service->dataList();
        $tableName = array_column($dataList, 'name');
        $count     = count($tableName);
        if ($count) {
            $this->output->writeln('+----------------------------- [自动备份] ----------------------------------+');
            $this->output->newLine();
            $this->output->newLine();
            $this->output->writeln(' 正在自动备份[start]');

            $data = [];
            foreach ($tableName as $i => $t) {

                $equalStr = str_repeat("=", $i);
                $space    = str_repeat(" ", $count - $i);
                $this->output->write("\r [$equalStr>$space]($i/$count%)");

                $res = $service->backup($t, 0);
                if ($res == false && $res != 0) {
                    $data [] = $t;
                }
            }

            $this->output->writeln("\r\n 备份结束[end]");

            if ($data && $g) {
                return $this->output->confirm($this->input, '自动备份表失败,失败数据库表:' . implode('|', $data) . ';是否继续执行?');
            }
            $this->output->newLine();
        }
        return true;
    }

    /**
     * 创建进度条
     * @param $percent
     * @return string
     */
    protected function buildLine($percent)
    {
        $repeatTimes = 100;
        if ($percent > 0) {
            $hasColor = str_repeat('■', $percent);
        } else {
            $hasColor = '';
        }

        if ($repeatTimes - $percent > 0) {
            $noColor = str_repeat(' ', $repeatTimes - $percent);
        } else {
            $noColor = '';
        }

        $buffer = sprintf("[{$hasColor}{$noColor}]");
        if ($percent !== 100) {
            $percentString = sprintf("[   %-6s]", $percent . '%');
        } else {
            $percentString = sprintf("[    %-5s]", 'OK');;
        }

        return $percentString . $buffer . "\r";
    }
}
