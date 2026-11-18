<?php
$consultas = [
    "A" => [
        "titulo" => "Departamentos",
        "descripcion" => "Consulta todos los departamentos registrados.",
        "sql" => "SELECT * FROM DEPARTAMENTO"
    ],
    "B" => [
        "titulo" => "Todos los empleados",
        "descripcion" => "Muestra los datos de cada empleado.",
        "sql" => "SELECT * FROM EMPLEADOS"
    ],
    "C" => [
        "titulo" => "Empleados de apellido López",
        "descripcion" => "Busca a las personas cuyo apellido es López.",
        "sql" => "SELECT * FROM EMPLEADOS WHERE Apellidos = 'López'"
    ],
    "D" => [
        "titulo" => "López y Pérez",
        "descripcion" => "Busca empleados de apellido López o Pérez.",
        "sql" => "SELECT * FROM EMPLEADOS WHERE Apellidos IN ('López', 'Pérez')"
    ],
    "E" => [
        "titulo" => "Personal de Manufactura",
        "descripcion" => "Muestra nombre y clave de quienes trabajan en el departamento 4.",
        "sql" => "SELECT Nombre, Clave_empleado FROM EMPLEADOS WHERE Clave_Depto = 4"
    ],
    "F" => [
        "titulo" => "Departamentos 1 y 3",
        "descripcion" => "Lista sus empleados en orden alfabético por nombre.",
        "sql" => "SELECT * FROM EMPLEADOS WHERE Clave_Depto IN (1, 3) ORDER BY Nombre"
    ],
    "G" => [
        "titulo" => "Apellidos que empiezan con H",
        "descripcion" => "Muestra el nombre y apellido de las personas cuyo apellido empieza con H.",
        "sql" => "SELECT Nombre, Apellidos FROM EMPLEADOS WHERE Apellidos LIKE 'H%'"
    ],
    "H" => [
        "titulo" => "Presupuesto de $50,000 a $70,000",
        "descripcion" => "Filtra los departamentos dentro de ese rango de presupuesto.",
        "sql" => "SELECT * FROM DEPARTAMENTO WHERE Presupuesto BETWEEN 50000 AND 70000"
    ],
    "I" => [
        "titulo" => "Empleados de Contabilidad",
        "descripcion" => "Busca empleados asignados al departamento de Contabilidad.",
        "sql" => "SELECT EMPLEADOS.Clave_empleado, EMPLEADOS.Nombre, EMPLEADOS.Apellidos
                  FROM EMPLEADOS
                  INNER JOIN DEPARTAMENTO
                    ON EMPLEADOS.Clave_Depto = DEPARTAMENTO.Clave_Depto
                  WHERE DEPARTAMENTO.Nombre = 'Contabilidad'"
    ],
    "J" => [
        "titulo" => "Empleado y departamento",
        "descripcion" => "Relaciona a cada empleado con el nombre de su departamento.",
        "sql" => "SELECT EMPLEADOS.Nombre, EMPLEADOS.Apellidos,
                         DEPARTAMENTO.Nombre AS Nombre_Departamento
                  FROM EMPLEADOS
                  INNER JOIN DEPARTAMENTO
                    ON EMPLEADOS.Clave_Depto = DEPARTAMENTO.Clave_Depto"
    ]
];
