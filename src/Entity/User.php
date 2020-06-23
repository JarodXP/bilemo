<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use App\Entity\Company;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Serializer\XmlRoot("user")
 * @Serializer\ExclusionPolicy("all")
 * @Hateoas\Relation(
 *          name="self",
 *          href=@Hateoas\Route(
 *             "api_user_details",
 *             parameters = {
 *             "id" = "expr(object.getId())"
 *             }),
 *          attributes={"method"="GET"},
 *          exclusion=@Hateoas\Exclusion(groups={"user-details"})
 * )
 * @Hateoas\Relation(
 *          name="Get user",
 *          href=@Hateoas\Route(
 *             "api_user_details",
 *             parameters = {
 *             "id" = "expr(object.getId())"
 *             }),
 *          attributes={"method"="GET"},
 *          exclusion=@Hateoas\Exclusion(groups={"users-list"})
 * )
 * @Hateoas\Relation(
 *          name="Remove user",
 *          href=@Hateoas\Route("api_remove_user",
 *             parameters = {
 *             "id" = "expr(object.getId())"
 *             }),
 *          attributes={"method"="DELETE"},
 *          exclusion=@Hateoas\Exclusion(groups={"users-list", "user-details"})
 * )
 * @Hateoas\Relation(
 *          name="Users list",
 *          href=@Hateoas\Route("api_users_list"),
 *          attributes={"method"="GET"},
 *          exclusion=@Hateoas\Exclusion(groups={"user-details"})
 * )
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\XmlAttribute
     * @Serializer\Expose()
     * @Serializer\Groups({"users-list", "user-details"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups({"user-details"})
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     * @Serializer\Groups({"users-list", "user-details"})
     */
    private ?string $firstName = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups({"users-list", "user-details"})
     */
    private ?string $lastName = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"user-details"})
     */
    private ?string $phoneNumber = null;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Serializer\Expose
     * @Serializer\Groups({"user-details"})
     */
    private DateTime $dateAdded;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Expose
     * @Serializer\Groups({"user-details"})
     */
    private ?Company $company = null;

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
