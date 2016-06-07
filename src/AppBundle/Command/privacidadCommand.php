<?php
namespace AppBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
 
class privacidadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('devvness:my_command')
            ->setDescription('Command description')
            ->addArgument('my_argument', InputArgument::OPTIONAL, 'Argument description');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $ejercicios = $em->getRepository('AppBundle:Ejercicio')
               ->findAll();
       
        foreach ($ejercicios as $ejer) {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $entity = $em->getRepository('AppBundle:Ejercicio')->findOneById($ejer->getId());
            if ($entity->getVisibilidad()=="privado") {
                $entity->setVisibilidad("publico");
            }
            $em->flush();
        }

        // Do whatever
        $output->writeln('Hello World');
        $em->flush();
    }
}