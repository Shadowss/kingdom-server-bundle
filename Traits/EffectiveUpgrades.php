<?php
/**
 * MIT License
 *
 * Copyright (c) 2017 Frisks
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace Kori\KingdomServerBundle\Traits;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kori\KingdomServerBundle\Entity\Technology;

trait EffectiveUpgrades
{

    /**
     * @var Collection
     */
    protected $effectiveUpgrades;

    /**
     * @return Collection
     */
    public function getEffectiveUpgrades(): Collection
    {
        return $this->effectiveUpgrades?: $this->effectiveUpgrades = new ArrayCollection();
    }

    /**
     * @param Technology $technology
     */
    public function addEffectiveUpgrade(Technology $technology)
    {
        if(!$this->getEffectiveUpgrades()->contains($technology))
            $this->getEffectiveUpgrades()->add($technology);
    }

    /**
     * @param Collection $effectiveUpgrades
     */
    public function setEffectiveUpgrades(Collection $effectiveUpgrades)
    {
        $this->effectiveUpgrades = $effectiveUpgrades;
    }

    /**
     * @param Technology $technology
     * @return bool
     */
    public function isAffected(Technology $technology): bool
    {
        return $this->effectiveUpgrades->contains($technology);
    }
}
