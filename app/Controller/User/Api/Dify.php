<?php
declare(strict_types=1);

namespace App\Controller\User\Api;

use App\Controller\Base\API\User;
use App\Entity\CreateObjectEntity;
use App\Entity\DeleteBatchEntity;
use App\Entity\QueryTemplateEntity;
use App\Interceptor\Business;
use App\Interceptor\Waf;
use App\Service\Query;
use App\Util\Date;
use Illuminate\Database\Eloquent\Relations\Relation;
use Kernel\Annotation\Inject;
use Kernel\Annotation\Interceptor;
use Kernel\Exception\JSONException;

#[Interceptor(Waf::class, Interceptor::TYPE_API)]
class Dify extends User
{
    #[Inject]
    private Query $query;

    public function getSaledSecretInfo(): array
    {
        $secret = trim(trim((string)$_POST['secret']), PHP_EOL);

        $map['equal-secret'] = $secret;
        $map['equal-status'] = 1;   //已经出售的
        $queryTemplateEntity = new QueryTemplateEntity();
        $queryTemplateEntity->setModel(\App\Model\Card::class);
        $queryTemplateEntity->setLimit(1);
        $queryTemplateEntity->setWhere($map);
        $data = $this->query->findTemplateAll($queryTemplateEntity)->toArray();
        if (count($data) > 0) {
            $json = $this->json(200, null, $data[0]);
        } else {
            $json = $this->json(200, null, null);
        }
        return $json;
    }

    // 更新卡密的剩余次数，目前用note字段来填充剩余次数
    public function decSaledSecretUsedCount(): array
    {
        $secret = trim(trim((string)$_POST['secret']), PHP_EOL);
        $map['equal-secret'] = $secret;
        $map['equal-status'] = 1;   //已经出售的
        $queryTemplateEntity = new QueryTemplateEntity();
        $queryTemplateEntity->setModel(\App\Model\Card::class);
        $queryTemplateEntity->setLimit(1);
        $queryTemplateEntity->setWhere($map);
        $data = $this->query->findTemplateAll($queryTemplateEntity)->toArray();
        if (count($data) > 0) {
            $note = intval($data[0]['note']);
            $note -= 1;
            
            \App\Model\Card::query()->whereIn('secret', $secret)->whereRaw("status=1")->update(['note' => strval($note)]);
        } 
        return $this->json(200, 'success');
    }


    
}