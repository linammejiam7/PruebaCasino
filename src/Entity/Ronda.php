<?php

namespace App\Entity;

use App\Repository\RondaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RondaRepository::class)]
class Ronda
{
    const GANADOR = "un jugador gano";
    const CASA_GANA = "La casa gana";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $numero_ronda;

    #[ORM\Column(type: 'datetime')]
    private $inicio;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $fin;

    #[ORM\OneToMany(mappedBy: 'ronda', targetEntity: RondaJugador::class)]
    private $rondaJugador;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroRonda(): ?int
    {
        return $this->numero_ronda;
    }

    public function setNumeroRonda(int $numero_ronda): self
    {
        $this->numero_ronda = $numero_ronda;

        return $this;
    }

    public function getInicio(): ?\DateTimeInterface
    {
        return $this->inicio;
    }

    public function setInicio(\DateTimeInterface $inicio): self
    {
        $this->inicio = $inicio;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(?\DateTimeInterface $fin): self
    {
        $this->fin = $fin;

        return $this;
    }
}
