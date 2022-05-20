<?php

namespace App\Controller;

use App\Helpers\Exceptions\UserException;
use App\Helpers\EnumManager\Enums\GeneralEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BaseController extends AbstractController
{
    protected TranslatorInterface $translator;
    protected SerializerInterface $serializer;
    protected ValidatorInterface $validator;
    protected LoggerInterface $logger;

    public function __construct(TranslatorInterface $translator, SerializerInterface $serializer, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    private function response(int $code, string $message = null, string $key, $data = null, array $custom = [])
    {
        $metaData = [
            'status' => $code,
            'message' => $message,
            'key' => $key,
            'error_id' => null
        ];

        $metaData = array_merge($metaData, $custom);

        return $this->json([
            'data' => $data,
            'metaData' => $metaData
        ], $code);
    }

    public function successResponse($data = null, string $message = null, string $key = GeneralEnum::SUCCESS, array $custom = [])
    {
        return $this->response(Response::HTTP_OK, $message, $key, $data, $custom);
    }

    public function exceptionResponse(\Throwable $e, $code = null)
    {
        // check whether the exception is user exception, So we can expose clear information about it
        if ($e instanceof UserException) {
            return $this->json([
                'data' => [],
                'metaData' => [
                    'status' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'key' => $e->getKey(),
                ]
            ], $e->getCode());
        }

        // storing the error into the log
        $this->logger->error($e->getMessage());

        $msg = $this->translator->trans('Server internal error');

        return $this->json([
            'data' => [],
            'metaData' => [
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $msg,
                'key' => GeneralEnum::INTERNAL_ERROR,
            ]
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function validateRequest(string $data, string $classValidator): ConstraintViolationListInterface
    {
        $requestPayload = $this->serializer->deserialize(
            $data,
            $classValidator,
            'json'
        );

        return $this->validator->validate($requestPayload);
    }

    public function createGenericErrorValidationResponse(ConstraintViolationListInterface $constraintViolationList): Response
    {
        $errors = [];
        /** @var ConstraintViolation $error */
        foreach ($constraintViolationList as $error) {
            $errors[$error->getPropertyPath()] = $error->getMessage();
        }

        return $this->response(Response::HTTP_BAD_REQUEST, implode(',', $errors), GeneralEnum::VALIDATION);
    }
}