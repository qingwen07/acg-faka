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

#[Interceptor([Waf::class, Business::class], Interceptor::TYPE_API)]
class Dify extends User
{
    #[Inject]
    private Query $query;

    public function checkSecretIsValid(): array
    {
        $secret = trim(trim((string)$_POST['secret']), PHP_EOL);

        $map['equal-secret'] = $secret;
        $queryTemplateEntity = new QueryTemplateEntity();
        $queryTemplateEntity->setModel(\App\Model\Card::class);
        $queryTemplateEntity->setWhere($map);
        $data = $this->query->findTemplateAll($queryTemplateEntity)->toArray();
        $json = $this->json(200, null, $data['data']);
        return $json;
    }


    
}