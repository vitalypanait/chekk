<?php

namespace App\Module\Common\Infrastructure\Request;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestResolver implements ValueResolverInterface
{
    protected ValidatorInterface $validator;
    protected SerializerInterface $serializer;

    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        $argumentType = $argument->getType();

        if (!$argumentType || !is_subclass_of($argumentType, IdentifierInterface::class)) {
            return [];
        }

        if ($request->getContentTypeFormat() !== 'json') {
            throw new \InvalidArgumentException('Invalid Content-type');
        }

        try {
            $object = $this->serializer->deserialize(
                $request->getContent(),
                $argument->getType(),
                'json',
                [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
            );
        } catch (NotEncodableValueException $e) {
            throw new \InvalidArgumentException('Error in json body: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new \InvalidArgumentException('Invalid request data: ' . $e->getMessage());
        }

        $errors = $this->validator->validate($object);

        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid validation');
        }

        return [$object];
    }
}
