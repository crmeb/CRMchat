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
namespace app\controller\admin\file;

use app\controller\admin\AuthController;
use app\services\system\attachment\SystemAttachmentServices;

/**
 * 图片管理类
 * Class SystemAttachment
 * @package app\controller\admin\file
 */
class Attachment extends AuthController
{
    protected $service;

    public function __construct(SystemAttachmentServices $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * 显示列表
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['pid', 0]
        ]);
        return $this->success($this->service->getImageList($where));
    }

    /**
     * 删除指定资源
     *
     * @param string $ids
     * @return \think\Response
     */
    public function delete()
    {
        [$ids] = $this->request->postMore([
            ['ids', '']
        ], true);
        $this->service->del($ids);
        return $this->success('删除成功');
    }

    /**
     * 图片上传
     * @param int $upload_type
     * @param int $type
     * @return mixed
     */
    public function upload($upload_type = 0, $type = 0)
    {
        [$pid, $file] = $this->request->postMore([
            ['pid', 0],
            ['file', 'file'],
        ], true);
        $res = $this->service->upload((int)$pid, $file, $upload_type, $type);
        return $this->success('上传成功', ['src' => $res]);
    }

    /**
     * 移动图片
     * @return mixed
     */
    public function moveImageCate()
    {
        $data = $this->request->postMore([
            ['pid', 0],
            ['images', '']
        ]);
        $this->service->move($data);
        return $this->success('移动成功');
    }

    /**
     * 修改文件名
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $realName = $this->request->post('real_name', '');
        if (!$realName) {
            return $this->fail('文件名称不能为空');
        }
        $this->service->update($id, ['real_name' => $realName]);
        return $this->success('修改成功');
    }

}
