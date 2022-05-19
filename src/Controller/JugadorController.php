<?php

namespace App\Controller;

use App\Entity\Jugador;
use App\Form\JugadorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;

class JugadorController extends AbstractController
{    
    #[Route('/registrarJugador', name: 'registrarJugador')]
    public function registrarJugador(Request $request, ManagerRegistry $doctrine): Response
    {
        $jugador = new Jugador();
        $form = $this->createForm(JugadorType::class, $jugador);

        // determino si el formulario fue enviado
        $form->handleRequest($request);   

        if($form->isSubmitted() && $form->isValid())
        {
            //entity manager guardar una entidad en la bd
            $em = $doctrine->getManager();                      
            $em->persist($jugador);
            $em->flush();
            
            //utilizando constantes
            $this->addFlash(type: 'exito', message: Jugador::REGISTRO_EXITOSO );
            return $this->redirectToRoute(route: 'registrarJugador'); 
        }
        return $this->render('jugador/index.html.twig', [
            'formulario' => $form->createView()
        ]);
    }

    #[Route('/listaJugadores', name: 'listaJugadores')]
    public function listaJugadores(ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();

        //obtener la lista de los usuarios registrados
        $jugadores = $em->getRepository(Jugador::class)->jugadoresActivos();

        //validar que hayan jugadores
        if(!$jugadores)
        {
            $this->addFlash(type: 'exito', message: Jugador::SIN_JUGADORES );
            return $this->redirectToRoute(route: 'registrarJugador'); 
        }
        return $this->render('jugador/listaJugadores.html.twig', [
            'jugadores' => $jugadores
        ]);
    }

    #[Route('/modificarJugador/{id}', name: 'modificarJugador')]
    public function modificarJugador(Request $request, $id, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $jugador = $em->getRepository(Jugador::class)->find($id);
        
        $form = $this->createForm(JugadorType::class, $jugador);
        $form->handleRequest($request);      

        if($form->isSubmitted() && $form->isValid())
        {                      
            $em->flush();
            $this->addFlash(type: 'exito', message: Jugador::ACTUALIZACION_EXITOSA );
        }
        
        return $this->render('jugador/modificarJugador.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/eliminarJugador/{id}', name: 'eliminarJugador')]
    public function eliminarJugador($id, ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();

        //buscar el jugador para eliminar
        $jugador = $em->getRepository(Jugador::class)->find($id);
        if(!$jugador)
        {
            $this->addFlash(type: 'exito', message: Jugador::ELIMINACION_ERROR );//utilizando constantes
            return new RedirectResponse($this->generateUrl('listaJugadores'));
            throw $this->createNotFoundException('El jugador con ID ' + $id + ' No existe');
        }
        $jugador->setActivo(0);
        //$em->remove($jugador);
        $em->flush();
        $this->addFlash(type: 'exito', message: Jugador::ELIMINACION_EXITOSA );//utilizando constantes
        return new RedirectResponse($this->generateUrl('listaJugadores'));
    }
}
