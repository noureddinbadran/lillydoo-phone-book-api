<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;

final class ContactValidator
{
    /**
     * @Assert\NotNull
     * @Assert\NotBlank(
            message = "First name can't be left blank"
     * )
     */
public string $first_name;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank(
            message = "Last name can't be left blank"
     * )
     */
public string $last_name;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank(
            message = "Address can't be left blank"
     * )
     */
public string $address;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank(
            message = "Phone number can't be left blank"
     * )
     */
public string $phone_number;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank(
            message = "Birthday can't be left blank"
     * )
     */
public string $birthday;

    /**
     * @Assert\NotNull
     * @Assert\NotBlank(
            message = "Email can't be left blank"
     * )
     */
public string $email;

    /**
     * @Assert\NotBlank(allowNull = true)
     */
public string $picture;

}
