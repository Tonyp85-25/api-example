<?php
namespace App\Serializer;

use ApiPlatform\Core\DataProvider\SerializerAwareDataProviderInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class UserAttributesNormalizer implements ContextAwareNormalizerInterface, SerializerAwareDataProviderInterface{
    use SerializerAwareTrait;

    const USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage =$tokenStorage;
    }

    public function normalize($object, ?string $format = null, array $context = [])
    {
        if($this->isUserHimself($object)){
            $context['groups'][] = 'get-owner';
        }
        return $this->passOn($object, $format,$context);
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        
    }

    public function supportsNormalization($data, ?string $format = null, array $context = [])
    {
        
    }
    public function setSerializerLocator(ContainerInterface $serializerLocator)
    {
        
    }

    private function isUserHimself($object)
    {

    }

    private function passOn($object, $format, $context)
    {
        if(!$this->serializer instanceof NormalizerInterface)
        {
            throw new \LogicException(sprintf('Cannot normalize object "%s" because the injected serializer is not a normalizer',$object)); 
        }
        $context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] = true;

        return $this->serializer->normalize($object,$format,$context);
    }
}