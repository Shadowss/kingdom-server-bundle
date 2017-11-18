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

namespace Kori\KingdomServerBundle\Service;


use Doctrine\ORM\EntityManager;
use Kori\KingdomServerBundle\Entity\Account;
use Kori\KingdomServerBundle\Entity\Avatar;
use Kori\KingdomServerBundle\Entity\BuildingLevel;
use Kori\KingdomServerBundle\Entity\Consumable;
use Kori\KingdomServerBundle\Entity\ConsumablesEffect;
use Kori\KingdomServerBundle\Entity\Field;
use Kori\KingdomServerBundle\Entity\Race;
use Kori\KingdomServerBundle\Entity\ServerStats;
use Kori\KingdomServerBundle\Entity\Town;
use Kori\KingdomServerBundle\Rules\AttackRuleInterface;
use Kori\KingdomServerBundle\Rules\BuildRuleInterface;

/**
 * Class Server
 * @package Kori\KingdomServerBundle\Service
 */
final class Server
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var int
     */
    protected $rate;

    /**
     * @var int
     */
    protected $protectionDays;

    /**
     * @var $buildRules
     */
    protected $buildRules = [];

    /**
     * @var AttackRuleInterface
     */
    protected $attackRule;

    /**
     * @var EffectManager
     */
    protected $effectManager;

    /**
     * Server constructor.
     * @param EntityManager $entityManager
     * @param int $rate
     * @param int $protectionDays
     */
    public function __construct(EntityManager $entityManager, int $rate, int $protectionDays)
    {
        $this->em = $entityManager;
        $this->rate = $rate;
        $this->protectionDays = $protectionDays;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->em;
    }

    /**
     * @return int
     */
    public function getRate(): int
    {
        return $this->rate;
    }

    /**
     * @param array $buildRules
     * @return void
     */
    public function setBuildRules(array $buildRules): void
    {
        $this->buildRules = $buildRules;
    }

    /**
     * @param AttackRuleInterface $attackRule
     */
    public function setAttackRule(AttackRuleInterface $attackRule)
    {
        $this->attackRule = $attackRule;
    }

    /**
     * Checks and build building
     *
     * @param Town $town
     * @param BuildingLevel $buildingLevel
     * @param $position
     * @return bool
     */
    public function build(Town $town, BuildingLevel $buildingLevel, int $position): bool
    {
        foreach($this->buildRules as $buildRule)
        {
            if($buildRule instanceof BuildRuleInterface && !$buildRule->comply($town, $buildingLevel, $position))
                return false;
        }
        return true;
    }

    /**
     * @param string $name
     * @return Account
     */
    public function createAccount(string $name): Account
    {
        $account = new Account();
        $account->setName($name);
        $account->setProtection( strtotime("+".$this->protectionDays."days"));

        $this->em->persist($account);
        $this->em->flush();

        return $account;
    }

    /**
     * @param string $name
     * @return ServerStats
     */
    public function getStatus(string $name): ?ServerStats
    {
        return $this->getEntityManager()->getRepository(ServerStats::class)->findOneBy(['name' => $name]);
    }


    /**
     * @return array
     */
    public function getRaces(): array
    {
        return $this->em->getRepository(Race::class)->findAll();
    }


    /**
     * @param int|null $typeFilter
     * @return int
     */
    public function getFieldsCount(int $typeFilter = null): int
    {
        return $this->em->getRepository(Field::class)->totalCount($typeFilter);
    }

    /**
     * @param int|null $typeFilter
     * @return array
     */
    public function getFields(int $typeFilter = null): array
    {
        return is_null($typeFilter)? $this->em->getRepository(Field::class)->findAll() : $this->em->getRepository(Field::class)->findBy(["type" => $typeFilter]);
    }

    /**
     * @param EffectManager $effectManager
     * @return void
     */
    public function setEffectManager(EffectManager $effectManager): void
    {
        $this->effectManager = $effectManager;
    }

    /**
     * @param Avatar $avatar
     * @param Consumable $consumable
     * @param bool $ignoreAway
     * @return bool
     */
    public function consume(Avatar $avatar, Consumable $consumable, bool $ignoreAway = false): bool
    {
        //@todo add check if avatar is carrying/have object
        if($avatar->isAway() && !$ignoreAway)
            return false;
        $consumable->getEffects()->filter(function (ConsumablesEffect $effect) use($avatar) {
            $this->effectManager->process($avatar, $effect->getType(), $effect->getValue());
        });
        $this->em->persist($avatar);
        $this->em->flush();

        return true;
    }


}
