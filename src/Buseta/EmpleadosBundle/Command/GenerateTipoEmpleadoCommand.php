<?php

namespace Buseta\EmpleadosBundle\Command;


use Buseta\NomencladorBundle\Entity\TipoEmpleado;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateTipoEmpleadoCommand extends ContainerAwareCommand
{
    private $tipoEmpleados = array(
        'chofer' => array(
            'nombre' => 'Chofer',
            'descripcion' => 'Chofer',
        ),
        'tecnico' => array(
            'nombre' => 'Técnico',
            'descripcion' => 'Técnico',
        )
    );

    protected function configure()
    {
        $this
            ->setName('busetaempleados:generate:tipoempleado')
            ->setDescription('Genera los datos para el nomenclador Tipo Empleado.')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $verbosity = $input->getOption('verbose');

        $nomencladores = $em->createQueryBuilder()
            ->select('tipoe.valor')
            ->from('BusetaNomencladorBundle:TipoEmpleado', 'tipoe')
            ->getQuery()
            ->getArrayResult();

        if (count($nomencladores) > 0) {
            (!$verbosity) ?: $output->writeln(sprintf('<info>Se encontraron %d nomenclador(es) Tipo Empleado.</info>', count($nomencladores)), OutputInterface::OUTPUT_NORMAL);

            $tipoEmpleadosOrigin = $this->tipoEmpleados;
            foreach ($nomencladores as $n) {
                foreach ($tipoEmpleadosOrigin as $valor => $tipoEmpleado) {
                    if ($valor === $n['valor']) {
                        unset($tipoEmpleadosOrigin[$valor]);
                    }
                }
            }

            $tipoEmpleadosPersist = $tipoEmpleadosOrigin;
        } else {
            $tipoEmpleadosPersist = $this->tipoEmpleados;
        }

        if (count($tipoEmpleadosPersist) > 0) {
            (!$verbosity) ?: $output->writeln(sprintf('<info>Deben adicionarce %d nomenclador(es) Tipo Empleado.</info>', count($tipoEmpleadosPersist)), OutputInterface::OUTPUT_NORMAL);
            foreach ($tipoEmpleadosPersist as $valor => $tipoEmpleado) {
                $ntipo = new TipoEmpleado();
                $ntipo->setValor($valor);
                $ntipo->setNombre($tipoEmpleado['nombre']);
                $ntipo->setDescripcion($tipoEmpleado['descripcion']);

                $em->persist($ntipo);
            }

            $em->flush();
        } else {
            (!$verbosity) ?: $output->writeln(sprintf('<error>No es necesario adicionar Tipos Empleado.</error>'), OutputInterface::OUTPUT_NORMAL);
        }

        (!$verbosity) ?: $output->writeln(sprintf('<info>Se ha completado el procesamiento del Nomenclador Tipo Empleado.</info>', count($tipoEmpleadosPersist)), OutputInterface::OUTPUT_NORMAL);
    }
}
