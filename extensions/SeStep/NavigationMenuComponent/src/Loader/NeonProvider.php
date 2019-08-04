<?php declare(strict_types=1);

namespace SeStep\NavigationMenuComponent\Loader;

use Nette\FileNotFoundException;
use Nette\Neon\Neon;
use Nette\Security\User;
use Nette\UnexpectedValueException;
use SeStep\Navigation\Menu\Items\ANavMenuItem;
use SeStep\Navigation\Menu\Items\INavMenuItem;
use SeStep\Navigation\Menu\Items\NavMenuLink;
use SeStep\Navigation\Menu\Items\NavMenuSeparator;

final class NeonProvider implements NavigationItemsProvider
{
    /** @var string */
    private $file;
    /** @var User */
    private $user;

    public function __construct(string $file, User $user)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        $this->file = $file;
        $this->user = $user;
    }

    /** @inheritDoc */
    public function getItems()
    {
        $data = Neon::decode(file_get_contents($this->file));

        return $this->parseItems($data['items']);
    }

    /**
     * @param array
     * @return ANavMenuItem[]
     */
    protected function parseItems($itemsData)
    {
        $items = [];
        foreach ($itemsData as $name => $item) {
            $requiredPermission = $item['requiredPermission'] ?? null;
            if ($requiredPermission) {
                if (!$this->user->isAllowed($requiredPermission)) {
                    continue;
                }
            }

            $items[$name] = $this->parseItem($item);
        }

        return $items;
    }

    private function parseItem($data): INavMenuItem
    {
        if (is_string($data)) {
            if ($data == '|') {
                return new NavMenuSeparator();
            }
        }
        if (is_array($data)) {
            $target = $data['target'] ?? '';
            $caption = $data['caption'];
            $icon = $data['icon'] ?? null;
            $params = $data['params'] ?? [];

            $navMenuLink = new NavMenuLink($target, $caption, $icon, $params);
            if (isset($data['subItems'])) {
                $subItems = $this->parseItems($data['subItems']);
                $navMenuLink->setItems($subItems);
            }

            return $navMenuLink;
        }

        throw new UnexpectedValueException("Unrecognized NavigationMenu item: " . gettype($data));
    }
}
