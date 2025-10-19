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
        $this->Image('', 10, 8, 30); // Puedes agregar un logo después
        // Título
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'FORMATO DE RECEPCION', 0, 1, 'C');
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
        $this->SetFillColor(200, 220, 255);
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
$pdf->TwoColumnRow('Hora de Recepcion:', $orden['hora_ingreso']);
$pdf->TwoColumnRow('Tecnico Asignado:', $orden['tecnico_asignado']);
$pdf->Ln(5);

// INFORMACIÓN DEL CLIENTE
$pdf->SectionTitle('INFORMACION DEL CLIENTE');
$pdf->TwoColumnRow('Nombre:', $orden['cliente']);
$pdf->TwoColumnRow('Telefono:', $orden['telefono']);
$pdf->TwoColumnRow('Email:', $orden['email']);
$pdf->TwoColumnRow('Direccion:', $orden['dir_cliente']);
$pdf->Ln(5);

// INFORMACIÓN DEL EQUIPO
$pdf->SectionTitle('INFORMACION DEL EQUIPO');
$pdf->TwoColumnRow('Tipo de Equipo:', $orden['tipo_equipo']);
$pdf->TwoColumnRow('Marca:', $orden['marca']);
$pdf->TwoColumnRow('Modelo:', $orden['modelo']);
$pdf->TwoColumnRow('Numero de Serie:', $orden['numero_serie']);
$pdf->TwoColumnRow('Color:', $orden['color']);
$pdf->TwoColumnRow('IMEI:', $orden['imei']);
$pdf->Ln(5);

// ESPECIFICACIONES TÉCNICAS
$pdf->SectionTitle('ESPECIFICACIONES TECNICAS');
$pdf->TwoColumnRow('Almacenamiento:', $orden['capacidad_almacenamiento']);
$pdf->TwoColumnRow('RAM:', $orden['ram']);
$pdf->TwoColumnRow('Procesador:', $orden['procesador']);
$pdf->TwoColumnRow('Sistema Operativo:', $orden['sistema_operativo'] . ' ' . $orden['version_so']);
$pdf->Ln(5);

// ESTADO FÍSICO
$pdf->SectionTitle('ESTADO FISICO DEL EQUIPO');
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 6, $orden['estado_fisico'] ?: 'Sin observaciones');
$pdf->Ln(2);

if($orden['golpes']) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, 'Golpes/Maltratos:');
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 6, $orden['golpes']);
}

if($orden['rayones']) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, 'Rayones:');
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 6, $orden['rayones']);
}

if($orden['faltan_tornillos']) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, 'Tornillos Faltantes:');
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 6, $orden['faltan_tornillos']);
}
$pdf->Ln(5);

// ACCESORIOS
$pdf->SectionTitle('ACCESORIOS QUE INGRESAN');
$pdf->SetFont('Arial', '', 10);

$accesorios = [];
if($orden['tiene_cargador']) $accesorios[] = 'Cargador original';
if($orden['funda_proteccion']) $accesorios[] = 'Funda/proteccion';
if($orden['cables_extra']) $accesorios[] = 'Cables: ' . $orden['cables_extra'];
if($orden['otros_accesorios']) $accesorios[] = $orden['otros_accesorios'];
if($orden['accesorios_ingreso']) $accesorios[] = $orden['accesorios_ingreso'];

if(count($accesorios) > 0) {
    foreach($accesorios as $accesorio) {
        $pdf->Cell(5);
        $pdf->Cell(5, 6, '•');
        $pdf->Cell(0, 6, $accesorio);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 6, 'No se ingresaron accesorios');
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
$pdf->Ln(5);

// PROBLEMA REPORTADO
$pdf->SectionTitle('PROBLEMA REPORTADO POR EL CLIENTE');
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 6, $orden['problemas_reportados']);
$pdf->Ln(5);

// OBSERVACIONES INICIALES
if($orden['observaciones_internas']) {
    $pdf->SectionTitle('OBSERVACIONES INICIALES DEL TECNICO');
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 6, $orden['observaciones_internas']);
    $pdf->Ln(5);
}

// TÉRMINOS Y CONDICIONES
$pdf->SectionTitle('TERMINOS Y CONDICIONES');
$pdf->SetFont('Arial', '', 9);
$terminos = [
    '• Despues de 30 dias no nos hacemos responsables de cualquier equipo dejado por el cliente.',
    '• Equipos mojados no hay garantia.',
    '• En pantalla 50 dias de garantia, por defecto de fabrica (No roto, mojado, estrellado, fundido, aplastado).',
    '• En baterias 30 dias de garantia, por defecto de fabrica (No baterias infladas).',
    '• Centro de carga 15 dias de garantia/ 30 dias placa completa por defecto de fabrica (No desoldado, mojado, quemado).',
    '• Software no hay garantia.',
    '• Equipos que se abren por enfrente llevan riesgo de quebrarse y no funcionar.',
    '• Equipos que no reciba el tecnico o apagados no nos hacemos responsables de cualquier falla extra no reportada por el cliente.',
    '• En recuperacion de archivos no se garantiza el 100% de recuperacion.',
    '• No hay ningun tipo de reembolsos, en caso de que se haya solicitado cualquier tipo de pieza/refaccion o cualquier otro tipo de abono.'
];

foreach($terminos as $termino) {
    $pdf->MultiCell(0, 5, $termino);
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

// GENERAR PDF
$pdf->Output('I', 'Recepcion_' . $orden['folio'] . '.pdf');
?>