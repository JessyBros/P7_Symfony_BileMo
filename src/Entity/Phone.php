<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 * 
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "phone",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true 
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          groups={"list_phones"}
 *      )
 * )
 * 
 * @Hateoas\Relation(
 *      "list",
 *      href = @Hateoas\Route(
 *          "phones",
 *          absolute = true 
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          groups={"show_phones"}
 *      )
 * )
 */
class Phone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"list_phones", "show_phones"})
     * @Groups("list_users")
     * @OA\Property(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"list_phones", "show_phones"})
     * @OA\Property(type="string", maxLength=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"show_phones"})
     * @OA\Property(type="string", maxLength=255)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     * @Serializer\Groups({"show_phones"})
     * @OA\Property(type="decimal, 6 number for 2 scale", maxLength=6)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"show_phones"})
     * @OA\Property(type="string", maxLength=255)
     */
    private $color;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Serializer\Groups({"show_phones"})
     * @OA\Property(type="decimal, 5 number for 2 scale")
     */
    private $size;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"show_phones"})
     * @OA\Property(type="integer")
     */
    private $weight;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

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

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }
}
