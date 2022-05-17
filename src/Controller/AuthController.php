<?php

namespace App\Controller;

use App\Helpers\Exceptions\UserException;
use App\Helpers\EnumManager\Enums\GeneralEnum;
use App\Repository\ClientRepository;
use App\Service\AuthService;
use App\Validator\ClientValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("api/auth")
 */
class AuthController extends BaseController
{
    /**
     * @Route("/register", methods="POST", name="register")
     * @OA\Post(
     *     path="/api/auth/register",
     *     description="Use this API to create a new client",
     * @OA\RequestBody(
     *      @OA\MediaType(
     *             mediaType="application/json",
     *          @OA\Schema(type="object",
     *              @OA\Property(property="username", type="string"),
     *              @OA\Property(property="password", type="string",description="password + 8 char",minLength=8),
     *              @OA\Property(property="first_name", type="string",minLength=3),
     *              @OA\Property(property="last_name", type="string",minLength=3),
     *
     *              example={"username": "nour-badran93@outlook.com",
     *                       "password": "12345678",
     *                       "first_name": "Nour Eddin",
     *                       "last_name": "Badran"
     *                      }
     *          )
     *         )
     * ),
     *     @OA\Response(
     *          response="200",
     *          description="Client created!"
     *      ),
     *     @OA\Response(
     *          response="400",
     *          description="Username has been take"
     *      ),
     *     @OA\Response(
     *          response="422",
     *          description="Mssing required data"
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Server internal error"
     *     )
     * )
     */
    public function register(Request $request, AuthService $authService, ClientValidator $clientValidator, TranslatorInterface $translator)
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data)
                throw new UserException($translator->trans('Mssing required data'), GeneralEnum::VALIDATION, Response::HTTP_UNPROCESSABLE_ENTITY);

            $validationRequest = $clientValidator->validateClient($data);

            if ($validationRequest->count() > 0)
                throw new UserException((string)$validationRequest, GeneralEnum::VALIDATION, Response::HTTP_BAD_REQUEST);

            if ($authService->register($data))
                return $this->successResponse(null, $translator->trans('Client created!'));
            else
                throw new UserException($translator->trans('something went wrong, try again'), GeneralEnum::INVALID,Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Throwable $e) {
            return $this->exceptionResponse($e);
        }
    }
}