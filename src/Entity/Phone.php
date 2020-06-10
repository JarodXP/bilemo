<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PhoneRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 */
class Phone
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"phone-list", "phone-details"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     *     minMessage = "The phone name length must be up to 2 characters",
     *     maxMessage = "The phone name length must be less than 50 characters")
     * @Groups({"phone-list", "phone-details"})
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     min = 3,
     *     max = 50,
     *     minMessage = "The supplier name length must be up to 3 characters",
     *     maxMessage = "The supplier name length must be less than 50 characters")
     * @Groups({"phone-list", "phone-details"})
     */
    private string $supplier;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     *     minMessage = "The reference length must be up to 2 characters",
     *     maxMessage = "The reference name length must be less than 50 characters")
     * @Groups({"phone-list", "phone-details"})
     */
    private string $productReference;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(
     *     min = 5,
     *     max = 50,
     *     minMessage = "The color length must be up to 2 characters",
     *     maxMessage = "The color length must be less than 50 characters")
     * @Groups({"phone-list", "phone-details"})
     */
    private string $color;

    /**
     * @ORM\Column(type="array")
     * @Groups({"phone-details"})
     */
    private array $features = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSupplier(): ?string
    {
        return $this->supplier;
    }

    public function setSupplier(string $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getProductReference(): ?string
    {
        return $this->productReference;
    }

    public function setProductReference(string $productReference): self
    {
        $this->productReference = $productReference;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getFeatures(): ?array
    {
        return $this->features;
    }

    public function setFeatures(array $features): self
    {
        $this->features = $features;

        return $this;
    }
}
