<?php
namespace App\Serializer;

use App\Entity\User;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class UserAttributesNormalizer implements ContextAwareNormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    const USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public function normalize($object, ?string $format = null, array $context = [])
    {
        if ($this->isUserHimself($object)) {
            $context['groups'][] = 'get-owner';
        }

        return $this->passOn($object, $format, $context);
    }

   

    public function supportsNormalization($data, ?string $format = null, array $context = [])
    {
        if (isset($context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof User;
    }
    
    private function isUserHimself($object)
    {
        return $object->getUsername() === $this->tokenStorage->getToken()->getUserIdentifier();
    }

    private function passOn($object, $format, $context)
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new \LogicException(sprintf('Cannot normalize object "%s" because the injected serializer is not a normalizer.', $object));
        }
        $context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] =true;
        return $this->serializer->normalize($object, $format, $context);
    }
}
