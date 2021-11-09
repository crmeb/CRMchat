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


use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\console\Command;

/**
 * Class Key
 * @package crmeb\command
 */
class Key extends Command
{

    protected function configure()
    {
        $this->setName('key')
            ->addOption('show', null, Option::VALUE_OPTIONAL, '为应用生成密码', true)
            ->addOption('force', null, Option::VALUE_OPTIONAL, '生成KEY')
            ->setDescription('生成KEY');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     */
    protected function execute(Input $input, Output $output)
    {
        $key = $this->generateRandomKey();

        if ($input->hasOption('show') && $input->getOption('show')) {
            $this->output->writeln('<comment>' . $key . '</comment>');
            return;
        }

        if (!$this->setKeyInEnvironmentFile($key)) {
            return;
        }

        $this->app->config->set(['app.key' => $key]);

        $this->output->info('Application key set successfully.');
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey()
    {
        return 'base64:' . base64_encode(
                \crmeb\utils\Encrypter::generateKey($this->app->config->get('app.cipher'))
            );
    }

    /**
     * Set the application key in the environment file.
     *
     * @param string $key
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $currentKey = $this->app->config->get('app.key');

        if (strlen($currentKey) !== 0 && (!$this->output->confirm($this->input, '在生产中的应用！是否重置?', false))) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * @return string
     */
    protected function environmentFilePath()
    {
        return root_path() . '.env';
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param string $key
     * @return void
     */
    protected function writeNewEnvironmentFileWith($key)
    {
        file_put_contents($this->environmentFilePath(), preg_replace(
            $this->keyReplacementPattern(),
            'APP_KEY = "' . $key . '"',
            file_get_contents($this->environmentFilePath())
        ));
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern()
    {
        $escaped = preg_quote(' = "' . $this->app->config->get('app.key') . '"', '/');

        return "/APP_KEY{$escaped}/m";
    }
}
