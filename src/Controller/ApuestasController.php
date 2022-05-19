<?php

namespace App\Controller;

use App\Entity\Jugador;
use App\Entity\Ronda;
use App\Entity\RondaJugador;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApuestasController extends AbstractController
{
    //metodo para iniciar una ronda del juego
    #[Route(name: 'mesa')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();        

        //envio jugadores registrados en eljuego
        $jugadores = $em->getRepository(Jugador::class)->jugadoresActivos();

        //validar si hay jugadores para habilitar la ronda de apuestas
        if($jugadores){

            //calcular valores minimos y maximos para apostar
            $carteraJugadores = array();

            foreach($jugadores as $j)
            {
                $min = $max = 0;

                if($j['cantidad_dinero'] > 1000)
                {
                    $min = $j['cantidad_dinero'] * 0.08;
                    $max = $j['cantidad_dinero'] * 0.15;

                }elseif($j['cantidad_dinero'] <= 1000 && $j['cantidad_dinero'] >0)
                {
                    $min = $max = $j['cantidad_dinero'];
                }                
                
                $carteraJugadores[] = ['id_jugador' => $j['id'],
                                        'min' => $min,
                                        'max' => $max ]; 

            }

            //obtengo el numero de la ultima ronda
            $num_ronda = $em->getRepository(Ronda::class)->numeroRondas();
            $numero_ronda = 0;

            //validacion en caso de que sea la primera ronda y si no aumento el valor de la ronda
            if($num_ronda === null){
                $numero_ronda = 1;
            }else{
                $numero_ronda = $num_ronda+1;
            }

            //creo registro de la ronda nueva
            $apuestaJugador = new Ronda();
            $apuestaJugador->setNumeroRonda($numero_ronda);
            $apuestaJugador->setInicio(new \DateTime());

            //persisto en la bd los datos de la ronda
            $em = $doctrine->getManager();                      
            $em->persist($apuestaJugador);
            $em->flush();

            return $this->render('apuestas/index.html.twig', [
                'jugadores' => $jugadores,
                'carteraJugadores' => $carteraJugadores,
                'ronda' => $numero_ronda,
                'id_ronda' => $apuestaJugador->getId()
            ]);
        }else{
            $this->addFlash(type: 'exito', message: Jugador::SIN_JUGADORES );
            return $this->redirectToRoute(route: 'registrarJugador'); 
        }
    }

    //metodo para realizar una apuesta de un jugador
    #[Route('/apuesta', name: 'apuesta', options: ['expose' => true])]
    public function apostar(Request $request, ManagerRegistry $doctrine)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $doctrine->getManager();

            //Obtengo datos enviados por el ajax
            $id = $request->request->get(key: 'id');
            $opcion = $request->request->get(key: 'opcion');
            $ronda = $request->request->get(key: 'ronda');
            $apuesta = $request->request->get(key: 'apuesta');

            //busco los datos de la variables en las consultas
            $jugador = $em->getRepository(Jugador::class)->find($id);
            $rondaActiva = $em->getRepository(Ronda::class)->find($ronda);

            //obtengo el valor de la cantidad de dinero disponible en el jugador
            $fondos = $jugador->getCantidadDinero();
            $cartera = 0;

            if($apuesta != 0)
            {
                //creo registro de la apuesta que hizo el jugador
                $apuestaJugador = new RondaJugador();
                $apuestaJugador->setApuesta($opcion);
                $apuestaJugador->setJugador($jugador);
                $apuestaJugador->setRonda($rondaActiva);

                $cartera = $fondos-$apuesta;
                $apuestaJugador->setDineroApuesta($apuesta);
                $em->persist($apuestaJugador);
                $jugador->setCantidadDinero($cartera);
                $em->flush();
            }            
            
            return new JsonResponse(['id'=>$id, 'cartera' => $cartera]);
        }
    }

    //metodo para iniciar la partida de azar en las opciones de la mesa Rojo, Negro y Verde
    #[Route('/inicioPartida', name: 'inicioPartida', options: ['expose' => true])]
    public function inicioPartida(Request $request, ManagerRegistry $doctrine)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $doctrine->getManager();

            //Obtengo datos enviados por el ajax
            $ronda = $request->request->get(key: 'ronda');

            //llamar la funcion ruleta para generar el color ganador
            $resultado_ruleta = $this->ruleta();

            //obtengo la ronda de jugadores ganadores
            $rondaActiva = $em->getRepository(RondaJugador::class)->listaGanadores($ronda, $resultado_ruleta);

            //fin de la ronda
            $rondaFin = $em->getRepository(Ronda::class)->find($ronda);
            $rondaFin->setFin(new \DateTime());

            $ganancia = 0;
            //id de ganadores para enviar mensaje por ajax
            $listaGanadores = array();
            $i = 0;
                
            if($rondaActiva){

                //filtrar ganadores y repartir ganancias
                foreach ($rondaActiva as $ganadores) 
                {
                    //obtener el valor id del gandor
                    $id_jugador = $ganadores->getJugador()->getId();
                    $listaGanadores[$i] = $id_jugador;

                    //objeto jugador ganador
                    $jugador = $em->getRepository(Jugador::class)->find($id_jugador);
                    $apuesta = $ganadores->getDineroApuesta();

                    if( $ganadores->getApuesta() == "Rojo" || $ganadores->getApuesta() == "Negro" ){
                        //Rojo 49% y Negro 49%.
                        $ganancia = ($apuesta*2) + $jugador->getCantidadDinero();
                    }else{
                        //Verde 2%.
                        $ganancia = ($apuesta*15) + $jugador->getDineroApuesta();          
                    }
                    
                    $jugador->setCantidadDinero($ganancia);
                    $em->flush();
                    $i++;      
                }
                //$this->addFlash(type: 'exito', message: Ronda::GANADOR );
            }else{
                $this->addFlash(type: 'exito', message: Ronda::CASA_GANA );//utilizando constantes
            }
            return new JsonResponse(['resultado' => $resultado_ruleta, 'lista' => $listaGanadores]);
        }
    }

    //Metodo de la ruleta aleatoria con probabilidades
    public function ruleta()
    {
        $probabilidades = array(0.49, 0.49, 0.02);
        $i = 0;
        $resultado = "";

        $aleatorio = rand(1,100)/100;

        if($aleatorio <= $probabilidades[2]){
            //probabilidad del Verde 2%
            $resultado = 'Verde';
        }elseif($aleatorio > $probabilidades[2] && $aleatorio <= $probabilidades[1]){
            //probabilidad del Negro <49%
            $resultado = 'Negro';
        }else{
            //probabilidad del Rojo >49%
            $resultado = 'Rojo';
        }   

        return $resultado;
    }
}
