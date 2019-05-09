<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Doctrine\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use PDO;

final class KeyPairHydrator extends AbstractHydrator
{
    protected function hydrateAllData()
    {
        return $this->_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
