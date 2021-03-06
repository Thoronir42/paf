<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;

use DateTime;
use LeanMapper\Entity;
use PAF\Modules\CommonModule\Model\CommentThread;
use PAF\Modules\DirectoryModule\Model\Person;
use PAF\Modules\CommonModule\Model\Slug;

/**
 * @property string $id
 * @property Person $supplier m:hasOne(supplier_person_id)
 * @property Slug $slug m:hasOne(slug)
 * @property string $status m:enum(CommissionWorkflow::STATUS_*) m:default('accepted')
 * @property Person $customer m:hasOne(customer_person_id)
 * @property Specification $specification m:hasOne(specification_id)
 * @property DateTime $acceptedOn
 * @property DateTime|null $archivedOn
 * @property DateTime|null $targetDelivery
 * @property CommentThread $comments m:hasOne(comment_thread_id)
 *
 */
class Commission extends Entity
{
    public function getState(): string
    {
        return $this->row->status;
    }

    public function setState(string $state): self
    {
        $this->row->status = $state;

        return $this;
    }
}
