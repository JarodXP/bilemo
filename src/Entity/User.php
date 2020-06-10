<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use App\Entity\Company;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"users-list", "user-details"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @Groups({"user-details"})
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     min = 1,
     *     max = 50,
     *     minMessage = "The first name length must be up to 1 characters",
     *     maxMessage = "The first name length must be less than 50 characters")
     * @Groups({"users-list", "user-details"})
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     min = 1,
     *     max = 50,
     *     minMessage = "The last name length must be up to 1 characters",
     *     maxMessage = "The last name length must be less than 50 characters")
     * @Groups({"users-list", "user-details"})
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="~^\+[0-9]{1,4}[\(0-9{1,10}\)]?[0-9]{4,30}$~",
     *     message="{value} is not a valid phone number.")
     *
     */
    private string $phoneNumber;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user-details"})
     */
    private DateTime $dateAdded;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull
     * @Groups({"user-details"})
     */
    private Company $company;

    public function __construct()
    {
        $this->dateAdded = new DateTime("now");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getDateAdded(): ?DateTime
    {
        return $this->dateAdded;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
