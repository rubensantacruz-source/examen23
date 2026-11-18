<?php
mysqli_report(MYSQLI_REPORT_OFF);
require_once __DIR__ . "/consultas.php";

$conexion = new mysqli("localhost", "root", "");
$error = "";
$resultado = null;
$consultaElegida = null;
$seleccion = isset($_POST["consulta"]) ? $_POST["consulta"] : "";

if ($conexion->connect_error) {
    $error = "No se pudo conectar con MySQL: " . $conexion->connect_error;
} elseif (!$conexion->query("CREATE DATABASE IF NOT EXISTS PERSONAL CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    $error = "No se pudo crear o abrir la base de datos: " . $conexion->error;
} elseif (!$conexion->select_db("PERSONAL")) {
    $error = "No se pudo seleccionar la base de datos PERSONAL: " . $conexion->error;
} elseif (!$conexion->set_charset("utf8mb4")) {
    $error = "No se pudo configurar la codificación: " . $conexion->error;
} else {
    $tablas = [
        "CREATE TABLE IF NOT EXISTS DEPARTAMENTO (
            Clave_Depto INT PRIMARY KEY,
            Nombre VARCHAR(30) NOT NULL,
            Presupuesto INT NOT NULL
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS EMPLEADOS (
            Clave_empleado VARCHAR(8) PRIMARY KEY,
            Nombre VARCHAR(30) NOT NULL,
            Apellidos VARCHAR(30) NOT NULL,
            Clave_Depto INT NOT NULL,
            CONSTRAINT FK_EMPLEADOS_DEPARTAMENTO
                FOREIGN KEY (Clave_Depto) REFERENCES DEPARTAMENTO(Clave_Depto)
        ) ENGINE=InnoDB"
    ];

    foreach ($tablas as $crearTabla) {
        if (!$conexion->query($crearTabla)) {
            $error = "No se pudieron preparar las tablas: " . $conexion->error;
            break;
        }
    }

    if (!$error) {
        $departamentos = $conexion->query("SELECT COUNT(*) AS total FROM DEPARTAMENTO");
        if (!$departamentos) {
            $error = "No se pudieron revisar los departamentos: " . $conexion->error;
        } elseif ((int) $departamentos->fetch_assoc()["total"] === 0) {
            $insertarDepartamentos = "INSERT INTO DEPARTAMENTO (Clave_Depto, Nombre, Presupuesto) VALUES
                (1, 'Personal', 80000),
                (2, 'Almacén', 70000),
                (3, 'Contabilidad', 60000),
                (4, 'Manufactura', 50000),
                (5, 'Empaque', 30000)";

            if (!$conexion->query($insertarDepartamentos)) {
                $error = "No se pudieron cargar los departamentos iniciales: " . $conexion->error;
            }
        }
    }

    if (!$error) {
        $empleados = $conexion->query("SELECT COUNT(*) AS total FROM EMPLEADOS");
        if (!$empleados) {
            $error = "No se pudieron revisar los empleados: " . $conexion->error;
        } elseif ((int) $empleados->fetch_assoc()["total"] === 0) {
            $insertarEmpleados = "INSERT INTO EMPLEADOS (Clave_empleado, Nombre, Apellidos, Clave_Depto) VALUES
                ('EMP01', 'Armando', 'López', 2),
                ('EMP02', 'Tatiana', 'Vargas', 1),
                ('EMP03', 'Laura', 'Iturria', 3),
                ('EMP04', 'Juan', 'Pérez', 4),
                ('EMP05', 'Iván', 'López', 4),
                ('EMP06', 'Margarita', 'Hernández', 1),
                ('EMP07', 'Jesús', 'Pérez', 3),
                ('EMP08', 'Tonatiuh', 'Flores', 2),
                ('EMP09', 'Juan', 'López', 4),
                ('EMP10', 'José', 'Hernández', 5)";

            if (!$conexion->query($insertarEmpleados)) {
                $error = "No se pudieron cargar los empleados iniciales: " . $conexion->error;
            }
        }
    }

    if (!$error && $seleccion !== "") {
        if (!isset($consultas[$seleccion])) {
            $error = "La consulta seleccionada no es válida.";
        } else {
            $consultaElegida = $consultas[$seleccion];
            $resultado = $conexion->query($consultaElegida["sql"]);

            if (!$resultado) {
                $error = "No se pudo ejecutar la consulta: " . $conexion->error;
            }
        }
    }
}

$conexion->close();

function escapar($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consultas de personal</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #172033;
            --muted: #64748b;
            --line: #e2e8f0;
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --surface: #ffffff;
            --background: #f4f6fb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--background);
            color: var(--ink);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }

        .hero {
            padding: 46px 24px 84px;
            background: linear-gradient(125deg, #312e81, #4f46e5 58%, #7c3aed);
            color: white;
        }

        .hero-inner, main {
            width: min(1120px, 100%);
            margin: 0 auto;
        }

        .eyebrow {
            margin: 0 0 10px;
            color: #c7d2fe;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(2rem, 5vw, 3.2rem);
            letter-spacing: -.04em;
        }

        .hero p:last-child {
            max-width: 650px;
            margin: 12px 0 0;
            color: #e0e7ff;
            font-size: 1.05rem;
            line-height: 1.6;
        }

        main {
            padding: 0 24px 56px;
            margin-top: -42px;
        }

        .panel {
            padding: 26px;
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 20px;
            background: var(--surface);
            box-shadow: 0 18px 50px rgba(30, 41, 59, .1);
        }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        h2 {
            margin: 0;
            font-size: 1.3rem;
            letter-spacing: -.02em;
        }

        .section-heading p, .card p, .empty p {
            margin: 7px 0 0;
            color: var(--muted);
            line-height: 1.5;
        }

        .count {
            flex: 0 0 auto;
            padding: 7px 11px;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: .84rem;
            font-weight: 700;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(225px, 1fr));
            gap: 14px;
        }

        .card {
            display: flex;
            min-height: 190px;
            flex-direction: column;
            align-items: flex-start;
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 15px;
            background: #fff;
            transition: border-color .2s, transform .2s, box-shadow .2s;
        }

        .card:hover {
            transform: translateY(-2px);
            border-color: #c7d2fe;
            box-shadow: 0 10px 24px rgba(79, 70, 229, .08);
        }

        .badge {
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border-radius: 10px;
            background: #eef2ff;
            color: var(--primary);
            font-weight: 800;
        }

        .card h3 {
            margin: 13px 0 0;
            font-size: 1rem;
        }

        .card p {
            margin-bottom: 16px;
            font-size: .9rem;
        }

        .source-code {
            width: 100%;
            margin: 0 0 16px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #f8fafc;
        }

        .source-code summary {
            padding: 10px 12px;
            color: #4338ca;
            cursor: pointer;
            font-size: .88rem;
            font-weight: 700;
        }

        .source-code pre {
            max-height: 220px;
            overflow: auto;
            margin: 0;
            padding: 12px;
            border-top: 1px solid var(--line);
            background: #111827;
            color: #e2e8f0;
            font: .82rem/1.55 Consolas, "Courier New", monospace;
            white-space: pre;
        }

        .copy-code {
            width: auto;
            margin: 10px 12px 12px;
            padding: 8px 11px;
            font-size: .82rem;
        }

        button {
            width: 100%;
            margin-top: auto;
            padding: 10px 14px;
            border: 0;
            border-radius: 10px;
            background: var(--primary);
            color: white;
            cursor: pointer;
            font: inherit;
            font-size: .92rem;
            font-weight: 700;
            transition: background .2s, transform .2s;
        }

        button:hover { background: var(--primary-dark); }
        button:focus-visible { outline: 3px solid #a5b4fc; outline-offset: 3px; }

        .result, .notice {
            margin-top: 24px;
            padding: 24px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: white;
        }

        .result-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .result-heading p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: .92rem;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--line);
            white-space: nowrap;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: .78rem;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: #f8faff; }

        .notice {
            border-color: #fecaca;
            background: #fff7f7;
            color: #991b1b;
        }

        .empty {
            padding: 30px 16px;
            text-align: center;
        }

        .empty-icon { font-size: 2rem; }

        @media (max-width: 600px) {
            .hero { padding: 34px 18px 72px; }
            main { padding: 0 14px 36px; }
            .panel, .result { padding: 18px; }
            .section-heading { align-items: flex-start; }
            .result-heading { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <header class="hero">
        <div class="hero-inner">
            <p class="eyebrow">Sistema de consultas</p>
            <h1>Personal y departamentos</h1>
            <p>Elige una consulta para ver sus resultados. La base de datos y los datos de ejemplo se preparan automáticamente desde PHP.</p>
        </div>
    </header>

    <main>
        <section class="panel" aria-labelledby="consultas-titulo">
            <div class="section-heading">
                <div>
                    <h2 id="consultas-titulo">Consultas disponibles</h2>
                    <p>Presiona un botón para consultar la información.</p>
                </div>
                <span class="count">10 consultas</span>
            </div>

            <div class="grid">
                <?php foreach ($consultas as $id => $consulta): ?>
                    <form class="card" method="post">
                        <span class="badge"><?= escapar($id) ?></span>
                        <h3><?= escapar($consulta["titulo"]) ?></h3>
                        <p><?= escapar($consulta["descripcion"]) ?></p>
                        <details class="source-code" <?= $seleccion === $id ? "open" : "" ?>>
                            <summary>Ver código fuente</summary>
                            <pre><code><?= escapar(trim($consulta["sql"])) ?></code></pre>
                            <button class="copy-code" type="button">Copiar código</button>
                        </details>
                        <button type="submit" name="consulta" value="<?= escapar($id) ?>">Ver resultados</button>
                    </form>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if ($error): ?>
            <section class="notice" role="alert">
                <strong>Ocurrió un problema</strong><br>
                <?= escapar($error) ?>
            </section>
        <?php elseif ($resultado instanceof mysqli_result && $consultaElegida): ?>
            <section class="result" aria-live="polite">
                <div class="result-heading">
                    <div>
                        <h2><?= escapar($consultaElegida["titulo"]) ?></h2>
                        <p><?= (int) $resultado->num_rows ?> resultado(s)</p>
                    </div>
                </div>

                <?php if ($resultado->num_rows > 0): ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <?php foreach ($resultado->fetch_fields() as $campo): ?>
                                        <th><?= escapar($campo->name) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($fila = $resultado->fetch_assoc()): ?>
                                    <tr>
                                        <?php foreach ($fila as $valor): ?>
                                            <td><?= escapar($valor) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No se encontraron registros para esta consulta.</p>
                <?php endif; ?>
                <?php $resultado->free(); ?>
            </section>
        <?php else: ?>
            <section class="result empty">
                <div class="empty-icon" aria-hidden="true">⌕</div>
                <h2>¿Qué información necesitas?</h2>
                <p>Selecciona cualquiera de las consultas para mostrar sus resultados aquí.</p>
            </section>
        <?php endif; ?>
    </main>
    <script>
        document.querySelectorAll(".copy-code").forEach(function (button) {
            button.addEventListener("click", async function () {
                var codigo = button.closest(".source-code").querySelector("code").textContent;

                try {
                    await navigator.clipboard.writeText(codigo);
                    button.textContent = "Código copiado";
                } catch (error) {
                    button.textContent = "Selecciona el código para copiarlo";
                }
            });
        });
    </script>
</body>
</html>
