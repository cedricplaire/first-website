<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StateRepository")
 */
class State
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * définit le nom du département au format chaîne de charactère de 128 de longueur.
     * 
     * @var String
     * 
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /** 
     * Liens avec les villes incluses dans ce département (entitée "Ville")
     * 
     * @Assert\NotBlank()
     * @Assert\Length(5)
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\City", mappedBy="state")
     */
    private $cities;

    /**
     * @ORM\Column(type="smallint")
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\City", mappedBy="region")
     */
    private $villes;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
        $this->villes = new ArrayCollection();
    }

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

    /**
     * @return Collection|City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setState($this);
        }

        return $this;
    }

    public function removeCity(City $city): self
    {
        if ($this->cities->contains($city)) {
            $this->cities->removeElement($city);
            // set the owning side to null (unless already changed)
            if ($city->getState() === $this) {
                $city->setState(null);
            }
        }

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection|City[]
     */
    public function getVilles(): Collection
    {
        return $this->villes;
    }

    public function addVille(City $ville): self
    {
        if (!$this->villes->contains($ville)) {
            $this->villes[] = $ville;
            $ville->setRegion($this);
        }

        return $this;
    }

    public function removeVille(City $ville): self
    {
        if ($this->villes->contains($ville)) {
            $this->villes->removeElement($ville);
            // set the owning side to null (unless already changed)
            if ($ville->getRegion() === $this) {
                $ville->setRegion(null);
            }
        }

        return $this;
    }
}
