<?php

namespace Espo\Modules\PreferredContact\Tools\Contact\Api;

use Espo\Core\Acl;
use Espo\Core\Api\Action;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Record\EntityProvider;
use Espo\Modules\Crm\Entities\Account;
use Espo\Modules\Crm\Entities\Contact;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\Part\Expression;

/**
 * @noinspection PhpUnused
 */
class PostToggleIsPreferred implements Action
{
    public function __construct(
        private Acl $acl,
        private EntityProvider $entityProvider,
        private EntityManager $entityManager,
    ) {}

    /**
     * @inheritDoc
     */
    public function process(Request $request): Response
    {
        $contactId = $request->getRouteParam('id') ?? throw new BadRequest();
        $accountId = $request->getParsedBody()->id ?? throw new BadRequest();

        if (!is_string($accountId) || !$accountId) {
            throw new BadRequest("Bad accountId.");
        }

        $contact = $this->entityProvider->getByClass(Contact::class, $contactId);
        $account = $this->entityProvider->getByClass(Account::class, $accountId);

        if (!$this->acl->checkEntityEdit($contact)) {
            throw new Forbidden();
        }

        if (!$this->acl->checkEntityEdit($account)) {
            throw new Forbidden();
        }

        $this->entityManager
            ->getRelation($contact, 'accounts')
            ->updateColumns(
                $account,
                [
                    'pcIsPreferred' => Expression::not(Expression::column('pcIsPreferred'))
                ]
            );

        return ResponseComposer::json(true);
    }
}
