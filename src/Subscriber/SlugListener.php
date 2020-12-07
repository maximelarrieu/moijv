<?php

namespace App\Subscriber;

use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugListener
{
    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * SlugListener constructor.
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if( ! in_array(get_class($entity), [Category::class, Tag::class])) {
            return;
        }

        if( ! $entity->getSlug()) {
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
        }
    }
}
