<?php

namespace App\Twig\Components;

use App\Entity\Tag;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

trait FormWithTagsTrait
{
    /**
     * @var Tag[]
     */
    #[LiveProp(writable: true)]
    public array $tags = [];

    #[ExposeInTemplate]
    public function getAllTags(): array
    {
        return $this->manager->getRepository(Tag::class)->findAll();
    }

    #[LiveListener('tag:created')]
    public function onTagCreated(#[LiveArg] Tag $tag): void
    {
        $this->tags[] = $tag;
    }

    public function isTagSelected(Tag $tag): bool
    {
        return \in_array($tag, $this->tags, true);
    }
}
