<?php

namespace App\Entity;

use App\Repository\JugadorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JugadorRepository::class)]
class Jugador
{
    const REGISTRO_EXITOSO  = "El jugador se ha registrado exitosamente";
    const ACTUALIZACION_EXITOSA  = "El jugador se ha actualizado exitosamente";
    const ELIMINACION_EXITOSA  = "El jugador se ha eliminado exitosamente";
    const ELIMINACION_ERROR  = "El jugador se ha eliminado exitosamente";
    const SIN_JUGADORES  = "No hay jugadores creados";
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 50)]
    private $email;

    #[ORM\Column(type: 'integer')]
    private $cantidad_dinero;

    #[ORM\Column(type: 'boolean')]
    private $activo;

    #[ORM\OneToMany(mappedBy: 'jugador', targetEntity: RondaJugador::class)]
    private $rondaJugador;

    public function __construct()
    {
        $this->cantidad_dinero = 10000;
        $this->activo = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
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

    public function getCantidadDinero(): ?int
    {
        return $this->cantidad_dinero;
    }

    public function setCantidadDinero(int $cantidad_dinero): self
    {
        $this->cantidad_dinero = $cantidad_dinero;

        return $this;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;

        return $this;
    }
}
