<?php

namespace Wyd2016Bundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Wyd2016Bundle\Entity\Language;

/**
 * Data transformer
 */
class LanguagesCollectionTransformer implements DataTransformerInterface
{
    /**
     * Transform
     *
     * @param Collection $languages languages
     *
     * @return array
     */
    public function transform($languages)
    {
        $array = array();
        foreach ($languages as $language) {
            /** @var Language $language */
            $array[] = $language->getSlug();
        }

        return $array;
    }

    /**
     * Reverse transform
     *
     * @param array $array
     *
     * @return ArrayCollection
     */
    public function reverseTransform($array)
    {
        $languages = new ArrayCollection();
        foreach ((array) $array as $slug) {
            if (preg_match('#^[a-z]{2}$#', $slug)) {
                $language = new Language();
                $language->setSlug($slug);
                $languages->add($language);
            }
        }

        return $languages;
    }
}
