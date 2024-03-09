<?php
declare(strict_types=1);

namespace App\Controller\User\Api;


use App\Controller\Base\API\User;
use App\Interceptor\UserVisitor;
use App\Interceptor\Waf;
use Kernel\Annotation\Get;
use Kernel\Annotation\Inject;
use Kernel\Annotation\Interceptor;
use Kernel\Annotation\Post;
use Kernel\Exception\JSONException;

#[Interceptor([Waf::class, UserVisitor::class])]
class Gpthub extends User
{
    public function userinfo(): array 
    {
        $user = $this->getUser();
        if (isset($user)) {
            return $this->json(200, "success", $this->getUser()->toArray());
        } else {
            return $this->json(400, "用户信息已过期，请重新登录");
        }
        
    }

    public function decSuanziCount(): array
    {
        if (isset($_POST['model']) && isset($_POST['token_cnt'])) {
            $model = $_POST['model'];
            $token_cnt = intval($_POST['token_cnt']);
            if (str_starts_with($model, 'gpt-3')) {
                $token_cnt = ceil($token_cnt / 200);
            } else if (str_starts_with($model, 'gpt-4')) {
                $token_cnt = ceil($token_cnt / 15);
            } else {    //其他模型暂时也按照gpt3.5的比率来算
                $token_cnt = intdiv($token_cnt, 200);
            }
            
            $user = $this->getUser();
            $user->gpt_suanzi_count -= $token_cnt;
            $user->save();
        } else {
            throw new JSONException("参数不正确");
        }
        return $this->json(200, 'success');
    }
}