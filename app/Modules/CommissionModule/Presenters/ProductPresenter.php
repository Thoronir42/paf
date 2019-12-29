<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Presenters;

use Nette\Application\BadRequestException;
use PAF\Common\BasePresenter;
use PAF\Modules\CommissionModule\Facade\ProductService;
use PAF\Modules\CommonModule\Components\PhotoBox\PhotoBoxControl;
use SeStep\NetteBootstrap\Controls\TabbedContent\TabbedContent;

class ProductPresenter extends BasePresenter
{
    /** @var ProductService @inject */
    public $productService;

    public function actionView(string $slug)
    {
        $product = $this->productService->getBySlug($slug);
        if (!$product) {
            throw new BadRequestException("commission.product.notFound");
        }

        $this->template->product = $product;


        $tabsControl = new TabbedContent();

        $photos = new PhotoBoxControl($product->photos);

        $tabsControl->addTab('photos', $photos, 'commission.product.photos');

        if ($product->commission) {
            $references = new PhotoBoxControl($product->commission->specification->references);
            $tabsControl->addTab('references', $references, 'commission.quote.references');
        }

        $this['productTabs'] = $tabsControl;
    }
}
