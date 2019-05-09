<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\EventListener;

use Maintainerati\Bikeshed\Entity\Note;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

final class NoteUpdateListener
{
    /** @var SerializerInterface */
    private $serializer;
    /** @var string */
    private $outputPath;

    public function __construct(SerializerInterface $serializer, string $outputPath)
    {
        $this->serializer = $serializer;
        $this->outputPath = $outputPath;
    }

    public function postUpdate(Note $entity): void
    {
        $fileName = $this->outputPath . \DIRECTORY_SEPARATOR . (string) $entity->getId() . '-' . time() . '.json';
        $fs = new Filesystem();
        if (!$fs->exists($this->outputPath)) {
            $fs->mkdir($this->outputPath);
        }
        $fs->dumpFile($fileName, $this->serializer->serialize([
            'id' => $entity->getId(),
            'note' => $entity->getNote(),
        ], 'json'));
    }
}
