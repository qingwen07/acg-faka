<?php
declare(strict_types=1);

namespace App\Controller\User\Api;


use App\Controller\Base\API\User;

#[Interceptor([Waf::class, UserVisitor::class])]
class Gpthub extends User
{
    public function userinfo(): array 
    {
        return $this->json(200, "success", $this->getUser());
    }
}