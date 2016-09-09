<?php
    class reporteMedicos {
        public function download($results){
            header('Content-type: application/vnd.ms-excel; charset=utf-8');
            header("Content-Disposition: attachment; filename=reporte_medicos_visitador.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo "<table border=1> ";
            echo "<tr> ";
            echo "<th>LISTA DE MEDICOS ASOCIADOS AL VISITADOR ".strtoupper($results[0]['empleado'])."</th> ";
            echo "</tr> ";
            echo "<tr> ";
            echo "<th> </th> ";
            echo "</tr> ";
            if ($results[0]['id_medico'] != "") {
                echo "<tr> ";
                echo "<th>CODIGO CLOSE-UP</th> ";
                echo "<th>CEDULA</th> ";
                echo "<th>NOMBRE</th> ";
                echo "<th>ESPECIALIDAD MEDICA</th> ";
                echo "</tr> ";
                foreach ($results as $valores) {
                    echo "<tr> ";
                    echo "<td>".$valores['id_medico']."</td> ";
                    echo "<td>".$valores['cedula']."</td> ";
                    echo "<td>".$valores['nombre']."</td> ";
                    echo "<td>".$valores['especialidad']."</td> ";
                    echo "</tr> ";
                }
            } else {
                echo "<tr> ";
                echo "<th>No existen medicos asociados</th> ";
                echo "</tr> ";
            }            
            echo "</table> ";
            exit();
        }
    }
    
?>