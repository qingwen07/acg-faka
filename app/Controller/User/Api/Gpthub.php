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
        return $this->json(200, "success", $this->getUser());
    }
}