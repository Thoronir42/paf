<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Facade;

use PAF\Common\AuditTrail\Facade\AuditTrailService;
use PAF\Common\Model\TransactionManager;
use PAF\Modules\CommissionModule\Model\Commission;
use PAF\Modules\CommissionModule\Model\Product;
use PAF\Modules\CommissionModule\Repository\ProductRepository;
use PAF\Modules\CommonModule\Model\Slug;
use PAF\Modules\CommonModule\Services\FilesService;
use SeStep\Moment\HasMomentProvider;

class ProductService
{
    use HasMomentProvider;

    /** @var ProductRepository */
    private $repository;
    /** @var TransactionManager */
    private $transactionManager;
    /** @var AuditTrailService */
    private $auditTrailService;
    /** @var FilesService */
    private $filesService;

    public function __construct(
        ProductRepository $repository,
        TransactionManager $transactionManager,
        AuditTrailService $auditTrailService,
        FilesService $filesService
    ) {
        $this->repository = $repository;
        $this->transactionManager = $transactionManager;
        $this->auditTrailService = $auditTrailService;
        $this->filesService = $filesService;
    }

    public function getTypes(): array
    {
        return [
            'partial' => 'commission.productType.partial',
            'halfSuit' => 'commission.productType.halfSuit',
            'fullSuit' => 'commission.productType.fullSuit',
        ];
    }

    public function getEnabledTypes(): array
    {
        return ['partial'];
    }

    public function createFromCommission(Commission $commission)
    {
        if ($this->productExists($commission->slug)) {
            return 'commission.product.alreadyExists';
        }

        $this->transactionManager->execute(function () use ($commission) {
            $product = new Product();

            $product->slug = $commission->slug;
            $product->type = $commission->specification->type;
            $product->title = $commission->specification->characterName;
            $product->description = $commission->specification->characterDescription;

            $product->owner = $commission->customer;
            $product->photos = $this->filesService->createThread(true);
            $product->commission = $commission;

            $this->repository->persist($product);

            $this->auditTrailService->addEvent($product->id, 'commission.product.createdBy', [
                'source' => ['commission', $commission->id],
            ]);
        });

        return null;
    }

    public function productExists(Slug $slug): bool
    {
        return (bool)$this->repository->findOneBy([
            'slug' => $slug->id,
        ]);
    }

    /**
     * @param string|Slug $slug
     */
    public function getBySlug($slug): ?Product
    {
        if ($slug instanceof Slug) {
            $slug = $slug->id;
        }
        if (!is_string($slug)) {
            throw new \InvalidArgumentException();
        }

        return $this->repository->findOneBy(['slug' => $slug]);
    }
}
