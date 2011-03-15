<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Encoder;

use Symfony\Component\Security\Core\User\AccountInterface;

/**
 * A generic encoder factory implementation
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class EncoderFactory implements EncoderFactoryInterface
{
    protected $encoders;

    public function __construct(array $encoders)
    {
        $this->encoders = $encoders;
    }

    /**
     * {@inheritDoc}
     */
    public function getEncoder(AccountInterface $account)
    {
        foreach ($this->encoders as $class => $encoder) {
            if (!$account instanceof $class) {
                continue;
            }

            if (!$encoder instanceof PasswordEncoderInterface) {
                return $this->encoders[$class] = $this->createEncoder($encoder);
            }

            return $this->encoders[$class];
        }

        throw new \RuntimeException(sprintf('No encoder has been configured for account "%s".', get_class($account)));
    }

    /**
     * Creates the actual encoder instance
     *
     * @param array $config
     * @return PasswordEncoderInterface
     */
    protected function createEncoder(array $config)
    {
        if (!isset($config['class'])) {
            throw new \InvalidArgumentException(sprintf('"class" must be set in %s.', json_encode($config)));
        }
        if (!isset($config['arguments'])) {
            throw new \InvalidArgumentException(sprintf('"arguments" must be set in %s.', json_encode($config)));
        }

        $reflection = new \ReflectionClass($config['class']);

        return $reflection->newInstanceArgs($config['arguments']);
    }
}