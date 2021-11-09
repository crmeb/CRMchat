<?php

use PullWord\PullWord;

// 兼容 5.1 版本
if (strpos(\think\App::VERSION, '5.1') !== false) {
    bind('pullword', PullWord::class);
}
