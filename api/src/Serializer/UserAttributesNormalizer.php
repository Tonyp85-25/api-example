<?php
namespace App\Serializer;

use ApiPlatform\Core\DataProvider\SerializerAwareDataProviderInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class UserAttributesNormalizer implements ContextAwareNormalizerInterface, SerializerAwareDataProviderInterface{

    public function normalize($object, ?string $format = null, array $context = [])
    {
        
    }

    public function setSerializerLocator(ContainerInterface $serializerLocator)
    {
        
    }

    public function supportsNormalization($data, ?string $format = null, array $context = [])
    {
        
    }
}