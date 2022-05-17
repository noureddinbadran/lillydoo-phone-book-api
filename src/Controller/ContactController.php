<?php

namespace App\Controller;

use App\Helpers\Exceptions\UserException;
use App\Helpers\EnumManager\Enums\GeneralEnum;
use App\Repository\ClientRepository;
use App\Service\AuthService;
use App\Service\ContactService;
use App\Validator\ClientValidator;
use App\Validator\ContactValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("api/contacts")
 */
class ContactController extends BaseController
{
    /**
     * @Route("", methods="POST", name="contacts.store")
     * @OA\Post(
     *     path="/api/contacts",
     *     description="Use this API to add a new contact",
     * @OA\RequestBody(
     *      @OA\MediaType(
     *             mediaType="application/json",
     *          @OA\Schema(type="object",
     *              @OA\Property(property="first_name", type="string",description="It represents the first name of the contact"),
     *              @OA\Property(property="last_name", type="string",description="It represents the last name of the contact"),
     *              @OA\Property(property="address", type="string",description="It represents the address of the contact"),
     *               @OA\Property(property="phone_number", type="string",description="It represents the phone number of the contact"),
     *               @OA\Property(property="birthday", type="date",description="It represents the birthday of the contact"),
     *               @OA\Property(property="email", type="string",description="It represents the email of the contact"),
     *               @OA\Property(property="picture", type="string",description="It represents the picture of the contact"),
     *
     *              example={"first_name": "Nour Eddin",
     *                       "last_name": "Badran",
     *                       "address": "Cairo - Egypt",
     *                       "phone_number": "00201125939067",
     *                       "birthday": "10-09-1993",
     *                       "email": "nour-badran93@outlook.com",
     *                       "picture": "picture",
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
     *          description="Phone number has been take"
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
    public function store(Request $request, ContactService $contactService): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $validationsErrors = $this->validateRequest($request->getContent(), ContactValidator::class);

            if ($validationsErrors->count() > 0) {
                return $this->createGenericErrorValidationResponse($validationsErrors);
            }

            $client = $this->getUser();
            $contactService->addNewContact($data, $client);
            return $this->successResponse();

        } catch (\Throwable $e) {
            return $this->exceptionResponse($e);
        }
    }
}