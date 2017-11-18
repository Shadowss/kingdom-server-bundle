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

namespace Kori\KingdomServerBundle\Tests\Units\Service;

use atoum\test;
use Kori\KingdomServerBundle\Service\GeneratorManager as TestedModel;
use Kori\KingdomServerBundle\Tests\Generators\CustomWorldGenerator;
use Kori\KingdomServerBundle\Tests\Generators\SampleWorldGenerator;

class GeneratorManager extends test
{

    public function testProcess()
    {
        $this
            ->given($generatorManager = new TestedModel())
            ->and($mockWorldGen = new \Kori\KingdomServerBundle\Generator\SampleWorldGenerator())
            ->and($mockSimilarGen = new SampleWorldGenerator())
            ->and($mockCustomWorldGen = new CustomWorldGenerator())
            ->when($process = $generatorManager->addGenerator($mockWorldGen))
            ->then(
                $this->boolean($process)->isTrue('Registration of generator should pass.')
            )
            ->when($generator = $generatorManager->findGenerator('mock\Kori\KingdomServerBundle\Generator\SampleWorldGenerator'))
            ->then(
                $this->object($generator)->isEqualTo($mockWorldGen, 'Should retrieve the same world generator registered using full class name')
            )
            ->when($generator = $generatorManager->findGenerator('SampleWorldGenerator'))
            ->then(
                $this->object($generator)->isEqualTo($mockWorldGen, 'Should retrieve the same world generator registered using class name')
            )
            ->when($generator = $generatorManager->findGenerator('SampleWorld'))
            ->then(
                $this->object($generator)->isEqualTo($mockWorldGen, 'Should retrieve the same world generator registered using short name')
            )
            ->when($process = $generatorManager->addGenerator($mockWorldGen))
            ->then(
                $this->boolean($process)->isFalse("Should not be able to double register a generator")
            )
            ->when($process = $generatorManager->addGenerator($mockSimilarGen))
            ->then(
                $this->boolean($process)->isFalse('Registration should fail because another Generator with name SampleWorldGenerator is already Registered')
            )
            ->when($process = $generatorManager->addGenerator($mockCustomWorldGen))
            ->then(
                $this->boolean($process)->isTrue('Registration should pass because name does not conflict')
            )
        ;

    }

}
