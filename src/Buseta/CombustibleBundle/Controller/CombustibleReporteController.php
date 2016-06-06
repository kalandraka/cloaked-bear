<?php

namespace Buseta\CombustibleBundle\Controller;

use Buseta\CombustibleBundle\Entity\ServicioCombustible;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_RichText;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Buseta\BusesBundle\Entity\Chofer;
use Buseta\BusesBundle\Entity\Vehiculo;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * CombustibleReporte controller.
 *
 * @Route("/combustible_reporte")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Combustible", routeName="servicioCombustible")
 */
class CombustibleReporteController extends Controller
{
    /**
     * Combustible by Chofer report
     *
     * @Route("/by_chofer", name="combustible_by_chofer")
     *
     * @Breadcrumb(title="Combustible por Chofer", routeName="combustible_by_chofer")
     */
    public function combustiblebyChoferAction(Request $request)
    {
        return $this->render('BusetaCombustibleBundle:Reporte:by_chofer.html.twig');
    }

    /**
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/ajax_combustible_by_chofer", name="ajax_combustible_by_chofer",
     *   options={"expose": true})
     * @Method({"GET"})
     */
    public function ajaxCombustibleByChoferAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaIni = null;
        $fechaFin = null;
        $chofer = null;
        if ($request->query->get('chofer') != "" && $request->query->get('chofer') != null) {
            $chofer = $request->query->get('chofer');
        }
        if ($request->query->get('fechaIni') != "" && $request->query->get('fechaIni') != null) {
            $dateIniArray = explode('/', $request->query->get('fechaIni'));
            $fechaIni = "$dateIniArray[2]-$dateIniArray[1]-$dateIniArray[0]";
        }
        if ($request->query->get('fechaFin') != "" && $request->query->get('fechaFin') != null) {
            $dateFinArray = explode('/', $request->query->get('fechaFin'));
            $fechaFin = "$dateFinArray[2]-$dateFinArray[1]-$dateFinArray[0]";
        }
        $servicios = $this->get('doctrine.orm.entity_manager')
            ->getRepository('BusetaCombustibleBundle:ServicioCombustible')->reportsFilter(
                $em,
                $fechaIni,
                $fechaFin,
                $chofer,
                'no'
            );
        $json_table_data = array();
        foreach ($servicios as $servicio) {
            $json_table_data[] = array(
                'id' => $servicio->getId(),
                'chofer' => $servicio->getChofer()->getNombreCompleto(),
                'vehiculo' => $servicio->getVehiculo()->__toString(),
                'combustible' => $servicio->getCombustible()->getCombustible()->getValor(),
                'litros' => $servicio->getCantidadLibros(),
                'fecha' => $servicio->getFecha()->format("d/m/Y"),
                'fechaToGraph' => $servicio->getFecha()->format("M d, Y H:i:s"),
            );
        }

        return new Response(
            json_encode(
                array('table_data' => $json_table_data, 'chofer' => $chofer),
                200
            )
        );
    }

    /**
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/excel_combustible_by_chofer", name="excel_combustible_by_chofer",
     *   options={"expose": true})
     * @Method({"GET"})
     */
    public function excelCombustibleByChoferAction(Request $request)
    {
        $objPHPExcel = new \PHPExcel();
        $em = $this->getDoctrine()->getManager();
        $fechaIni = null;
        $fechaFin = null;
        $chofer = null;
        $i = 1;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
            ->setSize(11);
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
        );
        $objPHPExcel->getActiveSheet()->getStyle('A1:A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Reporte de Combustible por Chofer');
        $i = $i + 1;
        if ($request->query->get('chofer') != "" && $request->query->get('chofer') != null) {
            $chofer = $request->query->get('chofer');
        }
        if ($request->query->get('fechaIni') != "" && $request->query->get('fechaIni') != null) {
            $dateIniArray = explode('/', $request->query->get('fechaIni'));
            $fechaIni = "$dateIniArray[2]-$dateIniArray[1]-$dateIniArray[0]";
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.'A'.$i)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Fecha inicial');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $request->query->get('fechaIni'));
            $i = $i + 1;
        }
        if ($request->query->get('fechaFin') != "" && $request->query->get('fechaFin') != null) {
            $dateFinArray = explode('/', $request->query->get('fechaFin'));
            $fechaFin = "$dateFinArray[2]-$dateFinArray[1]-$dateFinArray[0]";
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.'A'.$i)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Fecha final');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $request->query->get('fechaFin'));
            $i = $i + 1;
        }
        $servicios = $this->get('doctrine.orm.entity_manager')
            ->getRepository('BusetaCombustibleBundle:ServicioCombustible')->reportsFilter(
                $em,
                $fechaIni,
                $fechaFin,
                $chofer,
                'no'
            );
        $i = $i + 1;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "Chofer");
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, "Vehiculo");
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "Combustible");
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "Litros");
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "Fecha");
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.'E'.$i)->applyFromArray($styleArray);
        $i = $i + 1;
        // -- Fill with data
        foreach ($servicios as $servicio) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $servicio->getChofer()->getNombreCompleto());
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $servicio->getVehiculo()->__toString());
            $objPHPExcel->getActiveSheet()->setCellValue(
                'C'.$i,
                $servicio->getCombustible()->getCombustible()->getValor()
            );
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $servicio->getCantidadLibros());
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $servicio->getFecha()->format("d/m/Y"));
            $i = $i + 1;
        }
        // -- Fill with data
        // -- Autosize columns
        $fromCol = 'A';
        $toCol = 'F';
        for ($i = $fromCol; $i !== $toCol; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
            $calculatedWidth = $objPHPExcel->getActiveSheet()->getColumnDimension($i)->getWidth();
            $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setWidth((int)$calculatedWidth * 1.05);
        }
        $objPHPExcel->getActiveSheet()->calculateColumnWidths();
        // -- Autosize columns
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $curr_date = date('d_m_Y_His');
        $dir = "bundles/busetacombustible/reports";
        $file_ = "combustible_chofer_".$curr_date.".xls";
        if (!file_exists($dir) || !is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $objWriter->save(str_replace('.php', '.xlsx', $dir."/".$file_));

        return new Response(
            json_encode(
                array(
                    'result' => 'success',
                    'file_' => $file_,
                    'dir_' => $dir,
                )
            ), 200
        );
    }

    /**
     * Combustible by Chofer and Vehiculo report
     *
     * @Route("/by_chofer_and_vehic", name="comb_by_chofer_and_vehic")
     *
     * @Breadcrumb(title="Combustible por Chofer y Vehículo", routeName="comb_by_chofer_and_vehic")
     */
    public function combByChoferAndVehicAction(Request $request)
    {
        return $this->render('BusetaCombustibleBundle:Reporte:by_chofer_and_vehic.html.twig');
    }

    /**
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/ajax_comb_by_chofer_and_vehic", name="ajax_comb_by_chofer_and_vehic",
     *   options={"expose": true})
     * @Method({"GET"})
     */
    public function ajaxCombByChoferAndVehicAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaIni = null;
        $fechaFin = null;
        $chofer = null;
        $vehiculo = null;
        if ($request->query->get('chofer') != "" && $request->query->get('chofer') != null) {
            $chofer = $request->query->get('chofer');
        }
        if ($request->query->get('vehiculo') != "" && $request->query->get('vehiculo') != null) {
            $vehiculo = $request->query->get('vehiculo');
        }
        if ($request->query->get('fechaIni') != "" && $request->query->get('fechaIni') != null) {
            $dateIniArray = explode('/', $request->query->get('fechaIni'));
            $fechaIni = "$dateIniArray[2]-$dateIniArray[1]-$dateIniArray[0]";
        }
        if ($request->query->get('fechaFin') != "" && $request->query->get('fechaFin') != null) {
            $dateFinArray = explode('/', $request->query->get('fechaFin'));
            $fechaFin = "$dateFinArray[2]-$dateFinArray[1]-$dateFinArray[0]";
        }
        $servicios = $this->get('doctrine.orm.entity_manager')
            ->getRepository('BusetaCombustibleBundle:ServicioCombustible')->reportsFilter(
                $em,
                $fechaIni,
                $fechaFin,
                $chofer,
                $vehiculo
            );
        $json_table_data = array();
        foreach ($servicios as $servicio) {
            $json_table_data[] = array(
                'id' => $servicio->getId(),
                'chofer' => $servicio->getChofer()->getNombreCompleto(),
                'vehiculo' => $servicio->getVehiculo()->__toString(),
                'combustible' => $servicio->getCombustible()->getCombustible()->getValor(),
                'litros' => $servicio->getCantidadLibros(),
                'fecha' => $servicio->getFecha()->format("d/m/Y"),
                'fechaToGraph' => $servicio->getFecha()->format("M d, Y H:i:s"),
            );
        }

        return new Response(
            json_encode(
                array('table_data' => $json_table_data, 'chofer' => $chofer, 'vehiculo' => $vehiculo),
                200
            )
        );
    }

    /**
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/excel_comb_by_chofer_and_vehic", name="excel_comb_by_chofer_and_vehic",
     *   options={"expose": true})
     * @Method({"GET"})
     */
    public function excelCombByChoferAndVehicAction(Request $request)
    {
        $objPHPExcel = new \PHPExcel();
        $em = $this->getDoctrine()->getManager();
        $fechaIni = null;
        $fechaFin = null;
        $chofer = null;
        $vehiculo = null;
        $i = 1;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
            ->setSize(11);
        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
        );
        $objPHPExcel->getActiveSheet()->getStyle('A1:A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Reporte de Combustible por Chofer y Vehículo');
        $i = $i + 1;
        if ($request->query->get('chofer') != "" && $request->query->get('chofer') != null) {
            $chofer = $request->query->get('chofer');
        }
        if ($request->query->get('vehiculo') != "" && $request->query->get('vehiculo') != null) {
            $vehiculo = $request->query->get('vehiculo');
        }
        if ($request->query->get('fechaIni') != "" && $request->query->get('fechaIni') != null) {
            $dateIniArray = explode('/', $request->query->get('fechaIni'));
            $fechaIni = "$dateIniArray[2]-$dateIniArray[1]-$dateIniArray[0]";
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.'A'.$i)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Fecha inicial');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $request->query->get('fechaIni'));
            $i = $i + 1;
        }
        if ($request->query->get('fechaFin') != "" && $request->query->get('fechaFin') != null) {
            $dateFinArray = explode('/', $request->query->get('fechaFin'));
            $fechaFin = "$dateFinArray[2]-$dateFinArray[1]-$dateFinArray[0]";
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.'A'.$i)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Fecha final');
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $request->query->get('fechaFin'));
            $i = $i + 1;
        }
        $servicios = $this->get('doctrine.orm.entity_manager')
            ->getRepository('BusetaCombustibleBundle:ServicioCombustible')->reportsFilter(
                $em,
                $fechaIni,
                $fechaFin,
                $chofer,
                $vehiculo
            );
        $i = $i + 1;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "Chofer");
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, "Vehiculo");
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "Combustible");
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "Litros");
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "Fecha");
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.'E'.$i)->applyFromArray($styleArray);
        $i = $i + 1;
        // -- Fill with data
        foreach ($servicios as $servicio) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $servicio->getChofer()->getNombreCompleto());
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $servicio->getVehiculo()->__toString());
            $objPHPExcel->getActiveSheet()->setCellValue(
                'C'.$i,
                $servicio->getCombustible()->getCombustible()->getValor()
            );
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $servicio->getCantidadLibros());
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $servicio->getFecha()->format("d/m/Y"));
            $i = $i + 1;
        }
        // -- Fill with data
        // -- Autosize columns
        $fromCol = 'A';
        $toCol = 'F';
        for ($i = $fromCol; $i !== $toCol; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
            $calculatedWidth = $objPHPExcel->getActiveSheet()->getColumnDimension($i)->getWidth();
            $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setWidth((int)$calculatedWidth * 1.05);
        }
        $objPHPExcel->getActiveSheet()->calculateColumnWidths();
        // -- Autosize columns
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $curr_date = date('d_m_Y_His');
        $dir = "bundles/busetacombustible/reports";
        $file_ = "comb_chofer_vehic_".$curr_date.".xls";
        if (!file_exists($dir) || !is_dir($dir)) {
            mkdir($dir, 0777);
        }
        $objWriter->save(str_replace('.php', '.xlsx', $dir."/".$file_));

        return new Response(
            json_encode(
                array(
                    'result' => 'success',
                    'file_' => $file_,
                    'dir_' => $dir,
                )
            ), 200
        );
    }
}
