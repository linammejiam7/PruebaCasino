<?php

namespace App\Entity;

use App\Repository\RondaJugadorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RondaJugadorRepository::class)]
class RondaJugador
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 10)]
    private $apuesta;

    #[ORM\Column(type: 'integer')]
    private $dinero_apuesta;

    #[ORM\ManyToOne(targetEntity: Jugador::class, inversedBy: 'rondaJugador')]
    #[ORM\JoinColumn(nullable: false)]
    private $jugador;

    #[ORM\ManyToOne(targetEntity: Ronda::class, inversedBy: 'rondaJugador')]
    #[ORM\JoinColumn(nullable: false)]
    private $ronda;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApuesta(): ?string
    {
        return $this->apuesta;
    }

    public function setApuesta(string $apuesta): self
    {
        $this->apuesta = $apuesta;

        return $this;
    }

    public function getDineroApuesta(): ?int
    {
        return $this->dinero_apuesta;
    }

    public function setDineroApuesta(int $dinero_apuesta): self
    {
        $this->dinero_apuesta = $dinero_apuesta;

        return $this;
    }

    /**
     * Get the value of jugador
     */ 
    public function getJugador()
    {
        return $this->jugador;
    }

    /**
     * Set the value of jugador
     *
     * @return  self
     */ 
    public function setJugador($jugador)
    {
        $this->jugador = $jugador;

        return $this;
    }

    /**
     * Get the value of ronda
     */ 
    public function getRonda()
    {
        return $this->ronda;
    }

    /**
     * Set the value of ronda
     *
     * @return  self
     */ 
    public function setRonda($ronda)
    {
        $this->ronda = $ronda;

        return $this;
    }
}
