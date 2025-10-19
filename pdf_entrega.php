<?php
require_once('fpdf/fpdf.php');
include 'config.php';

if(!isset($_GET['orden'])) {
    die('No se especificó la orden');
}

$orden_id = $_GET['orden'];

// OBTENER DATOS DE LA ORDEN
$stmt = $pdo->prepare("
    SELECT o.*, e.*, c.nombre as cliente, c.telefono, c.email, c.direccion as dir_cliente
    FROM ordenes_servicio o 
    JOIN equipos e ON o.id_equipo = e.id_equipo 
    JOIN clientes c ON e.id_cliente = c.id_cliente 
    WHERE o.id_orden = ?
");
$stmt->execute([$orden_id]);
$orden = $stmt->fetch();

if(!$orden) {
    die('Orden no encontrada');
}

// CREAR PDF
class PDF extends FPDF {
    function Header() {
        // Logo
        $this->Image('', 10, 8, 30);
        // Título
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'FORMATO DE ENTREGA', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'Taller Lykos - Servicio Tecnico Especializado', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Av. Niños Heroes 43, Doctores, Ciudad de Mexico', 0, 1, 'C');
        $this->Cell(0, 6, 'Telefonos: 55-9191-1406 // 56-4117-0209', 0, 1, 'C');
        $this->Cell(0, 6, 'Email: reparacioncomputadorasmexico@gmail.com', 0, 1, 'C');
        $this->Ln(5);
        
        // Línea separadora
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(8);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . ' - Formato generado el ' . date('d/m/Y H:i'), 0, 0, 'C');
    }
    
    function SectionTitle($title) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(220, 255, 220);
        $this->Cell(0, 8, $title, 0, 1, 'L', true);
        $this->Ln(2);
    }
    
    function TwoColumnRow($label, $value, $width1 = 60, $width2 = 120) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($width1, 6, $label);
        $this->SetFont('Arial', '', 10);
        $this->Cell($width2, 6, $value);
        $this->Ln();
    }
}

$pdf = new PDF();
$pdf->AddPage();

// INFORMACIÓN DE LA ORDEN
$pdf->SectionTitle('INFORMACION DE LA ORDEN');
$pdf->TwoColumnRow('Folio:', $orden['folio']);
$pdf->TwoColumnRow('Fecha de Recepcion:', date('d/m/Y', strtotime($orden['fecha_ingreso'])));
if($orden['fecha_entrega_real']) {
    $pdf->TwoColumnRow('Fecha de Entrega:', date('d/m/Y', strtotime($orden['fecha_entrega_real'])));
} else {
    $pdf->TwoColumnRow('Fecha de Entrega:', date('d/m/Y'));
}
$pdf->TwoColumnRow('Estado Final:', $orden['estado_orden']);
$pdf->TwoColumnRow('Tecnico Responsable:', $orden['tecnico_asignado']);
$pdf->Ln(5);

// INFORMACIÓN DEL CLIENTE
$pdf->SectionTitle('INFORMACION DEL CLIENTE');
$pdf->TwoColumnRow('Nombre:', $orden['cliente']);
$pdf->TwoColumnRow('Telefono:', $orden['telefono']);
$pdf->TwoColumnRow('Email:', $orden['email']);
$pdf->Ln(5);

// INFORMACIÓN DEL EQUIPO
$pdf->SectionTitle('EQUIPO REPARADO');
$pdf->TwoColumnRow('Tipo de Equipo:', $orden['tipo_equipo']);
$pdf->TwoColumnRow('Marca:', $orden['marca']);
$pdf->TwoColumnRow('Modelo:', $orden['modelo']);
$pdf->TwoColumnRow('Numero de Serie:', $orden['numero_serie']);
$pdf->TwoColumnRow('Color:', $orden['color']);
$pdf->Ln(5);

// TRABAJO REALIZADO
$pdf->SectionTitle('TRABAJO REALIZADO');
if($orden['trabajo_realizado']) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 6, $orden['trabajo_realizado']);
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 6, 'No se especificó trabajo realizado');
}
$pdf->Ln(5);

// DIAGNÓSTICO FINAL
if($orden['diagnostico']) {
    $pdf->SectionTitle('DIAGNOSTICO FINAL');
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 6, $orden['diagnostico']);
    $pdf->Ln(5);
}

// PIEZAS UTILIZADAS
if($orden['piezas_necesarias']) {
    $pdf->SectionTitle('PIEZAS Y REPUESTOS UTILIZADOS');
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 6, $orden['piezas_necesarias']);
    $pdf->Ln(5);
}

// ACCESORIOS DEVUELTOS
$pdf->SectionTitle('ACCESORIOS DEVUELTOS');
$pdf->SetFont('Arial', '', 10);

$accesorios_devueltos = [];
if($orden['tiene_cargador']) $accesorios_devueltos[] = 'Cargador original';
if($orden['funda_proteccion']) $accesorios_devueltos[] = 'Funda/proteccion';
if($orden['cables_extra']) $accesorios_devueltos[] = 'Cables: ' . $orden['cables_extra'];
if($orden['otros_accesorios']) $accesorios_devueltos[] = $orden['otros_accesorios'];
if($orden['accesorios_ingreso']) $accesorios_devueltos[] = $orden['accesorios_ingreso'];

if(count($accesorios_devueltos) > 0) {
    foreach($accesorios_devueltos as $accesorio) {
        $pdf->Cell(5);
        $pdf->Cell(5, 6, '✓');
        $pdf->Cell(0, 6, $accesorio);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 6, 'No se devolvieron accesorios');
    $pdf->Ln();
}

if($orden['numero_serie_cargador']) {
    $pdf->Cell(10);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, 6, 'Serie del cargador:');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, $orden['numero_serie_cargador']);
    $pdf->Ln();
}
$pdf->Ln(8);

// INFORMACIÓN DE COSTOS
$pdf->SectionTitle('INFORMACION DE COSTOS');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 8, 'Concepto', 1, 0, 'C');
$pdf->Cell(40, 8, 'Monto', 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(80, 8, 'Costo de Reparacion', 1, 0, 'L');
$pdf->Cell(40, 8, '$ ' . number_format($orden['costo_final'], 2), 1, 1, 'R');

if($orden['anticipo'] > 0) {
    $pdf->Cell(80, 8, 'Anticipo Recibido', 1, 0, 'L');
    $pdf->Cell(40, 8, '$ ' . number_format($orden['anticipo'], 2), 1, 1, 'R');
    
    $pdf->Cell(80, 8, 'Saldo Pendiente', 1, 0, 'L');
    $pdf->Cell(40, 8, '$ ' . number_format($orden['saldo_pendiente'], 2), 1, 1, 'R');
}

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(80, 10, 'TOTAL', 1, 0, 'C');
$pdf->Cell(40, 10, '$ ' . number_format($orden['costo_final'], 2), 1, 1, 'R');
$pdf->Ln(8);

// GARANTÍA
$pdf->SectionTitle('POLITICA DE GARANTIA');
$pdf->SetFont('Arial', '', 9);
$garantia = [
    '• La garantia cubre unicamente los repuestos instalados y la mano de obra.',
    '• Periodo de garantia: 30 dias a partir de la fecha de entrega.',
    '• La garantia no cubre danos por mal uso, accidentes, liquidos, o modificaciones no autorizadas.',
    '• Para hacer valida la garantia, el cliente debe presentar este formato de entrega.',
    '• No hay reembolsos una vez realizado el trabajo.',
    '• El taller no se responsabiliza por datos o software del equipo.'
];

foreach($garantia as $item) {
    $pdf->MultiCell(0, 5, $item);
}
$pdf->Ln(8);

// FIRMAS
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 8, 'FIRMA DEL CLIENTE', 'T', 0, 'C');
$pdf->Cell(10, 8, '');
$pdf->Cell(85, 8, 'FIRMA DEL TECNICO', 'T', 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(95, 5, 'Nombre: ' . $orden['cliente'], 0, 0, 'C');
$pdf->Cell(10, 5, '');
$pdf->Cell(85, 5, 'Tecnico: ' . $orden['tecnico_asignado'], 0, 1, 'C');
$pdf->Cell(95, 5, 'Fecha: ' . date('d/m/Y'), 0, 0, 'C');
$pdf->Cell(10, 5, '');
$pdf->Cell(85, 5, 'Fecha: ' . date('d/m/Y'), 0, 1, 'C');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, 'El cliente confirma que ha recibido el equipo en buenas condiciones y esta de acuerdo con el trabajo realizado.', 0, 1, 'C');

// GENERAR PDF
$pdf->Output('I', 'Entrega_' . $orden['folio'] . '.pdf');
?>