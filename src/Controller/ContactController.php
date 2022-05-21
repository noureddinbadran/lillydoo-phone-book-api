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
     * @Route("", methods={"GET"}, name="contacts.index")
     * @OA\Get(
     *     path="/api/contacts",
     *     description="Use this API to get the contacts",
     * @OA\Parameter(
     *   name="page",
     *   description="Paginate the contacts.",
     *   required=false,
     *   in="query",
     *   @OA\Schema(
     *       type="integer"
     *   )
     * ),
     * @OA\Parameter(
     *   name="limit",
     *   description="Limit number of the contacts within one request",
     *   required=false,
     *   in="query",
     *   @OA\Schema(
     *       type="integer"
     *   )
     * ),
     *     @OA\Response(
     *          response="200",
     *          description="You will receive an array of the contacts"
     *      ),
     * )
     */
    public function index(Request $request, ContactService $contactService)
    {
        $contacts = $contactService->getContacts($request->get('page', 1), $request->get('limit', 5));
        return $this->successResponse($contacts);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="contacts.show")
     * @OA\Get(
     *     path="/api/contacts/{id}",
     *     description="Use this API to get a specific contact",
     *     @OA\Response(
     *          response="200",
     *          description="You will receive an object of the contact"
     *      ),
     * )
     */
    public function show(Request $request, ContactService $contactService, $id)
    {
        try
        {
            $contact = $contactService->findOneBy(['id' => $id]);
            if(!$contact)
                throw new UserException($this->translator->trans('Contact not found'), GeneralEnum::NOT_FOUND, Response::HTTP_NOT_FOUND);
            return $this->successResponse($contact);
        } catch (\Throwable $e) {
            return $this->exceptionResponse($e);
        }
    }

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
     *          description="Contact created!"
     *      ),
     *     @OA\Response(
     *          response="400",
     *          description="Phone number has been take"
     *      ),
     *     @OA\Response(
     *          response="422",
     *          description="Missing required data"
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
            $contactService->createOrUpdateContact($data);
            return $this->successResponse();

        } catch (\Throwable $e) {
            echo $e->getMessage();
            die();
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @Route("/{id}", methods="PUT", name="contacts.update")
     * @OA\Put(
     *     path="/api/contacts/{id}",
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
     *          description="Contact created!"
     *      ),
     *     @OA\Response(
     *          response="400",
     *          description="Phone number has been take"
     *      ),
     *     @OA\Response(
     *          response="422",
     *          description="Missing required data"
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Server internal error"
     *     )
     * )
     */
    public function update(Request $request, ContactService $contactService, $id)
    {
        try
        {
            $data = json_decode($request->getContent(), true);

            $validationsErrors = $this->validateRequest($request->getContent(), ContactValidator::class);

            if ($validationsErrors->count() > 0) {
                return $this->createGenericErrorValidationResponse($validationsErrors);
            }

            $contact = $contactService->findOneBy(['id' => $id]);
            if(!$contact)
                throw new UserException($this->translator->trans('Contact not found'), GeneralEnum::NOT_FOUND, Response::HTTP_NOT_FOUND);

            $contact = $contactService->createOrUpdateContact($data, $contact);
            return $this->successResponse($contact);
        } catch (\Throwable $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="contacts.destroy")
     * @OA\Delete(
     *     path="/api/contacts/{id}",
     *     description="Use this API to delete a specific contact",
     *     @OA\Response(
     *          response="200",
     *          description="You will delete a specific contact"
     *      ),
     * )
     */
    public function destroy(Request $request, ContactService $contactService, $id)
    {
        try {
        $contact = $contactService->destroy($id);
        return $this->successResponse($contact);
        } catch (\Throwable $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @Route("/search/{name}", methods={"GET"}, name="contacts.search")
     * @OA\Get(
     *     path="/api/contacts/search/{name}",
     *     description="Use this API to search for contacts by name",
     * @OA\Parameter(
     *   name="page",
     *   description="Paginate the contacts.",
     *   required=false,
     *   in="query",
     *   @OA\Schema(
     *       type="integer"
     *   )
     * ),
     * @OA\Parameter(
     *   name="limit",
     *   description="Limit number of the contacts within one request",
     *   required=false,
     *   in="query",
     *   @OA\Schema(
     *       type="integer"
     *   )
     * ),
     *     @OA\Response(
     *          response="200",
     *          description="You will get an array of matched objects"
     *      ),
     * )
     */
    public function search(Request $request, ContactService $contactService, $name)
    {
            $contacts = $contactService->searchByName($name, $request->get('page', 1), $request->get('limit', 5));
            return $this->successResponse($contacts);
    }
}