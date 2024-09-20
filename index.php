<?php
session_start(); // Iniciar sesión para almacenar el nombre del usuario y otras variables

// Función para generar una multiplicación única
function generarMultiplicacionUnica() {
    do {
        $numero1 = rand(2, 9);
        $numero2 = rand(2, 9);
        $multiplicacion = "$numero1 x $numero2";
    } while (in_array($multiplicacion, $_SESSION['multiplicacionesRealizadas'] ?? []));

    // Almacenar la multiplicación para evitar repeticiones
    $_SESSION['multiplicacionesRealizadas'][] = $multiplicacion;
    
    return [$numero1, $numero2];
}

// Función para mostrar el formulario de nombre
function mostrarFormularioNombre($mensajeError = '') {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Multiplicando Ando</title>
        <link rel="stylesheet" href="styles.css">
        <link rel="shortcut icon" href="./img/X.png" type="image/x-icon">
    </head>
    <body>
        <h1>Multiplicando Ando</h1>
        <h2>Hola, ¡qué bueno que quieras repasar las tablas de multiplicar! <br> Iniciemos con una pregunta muy fácil.</h3>
        <?php if ($mensajeError): ?>
            <p style="color: red;"><?php echo $mensajeError; ?></p>
        <?php endif; ?>
        <form action="index.php" method="post" id="nombreMultiplicacion">
            <label for="my_name">¿Cómo te llamas?</label>
            <input type="text" name="my_name" id="my_name" required>
            <h4><strong>IMPORTANTE:</strong> Por cada 5 correctas ganas una moneda, pero por cada 3 incorrectas pierdes una moneda.</h4>
            <button type="submit">Iniciemos</button>
        </form>
    </body>
    </html>
    <?php
}

// Función para mostrar el formulario de multiplicación
function mostrarFormularioMultiplicacion($numero1, $numero2) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Multiplicando Ando</title>
        <link rel="stylesheet" href="styles.css">
        <link rel="shortcut icon" href="./img/X.png" type="image/x-icon">
        <script>
            window.onload = function() {
                document.getElementById('respuesta').focus(); // Enfocar el cuadro de respuesta automáticamente
            }
        </script>
    </head>
    <body>
        <h1>Multiplicando Ando</h1>
        <h3><?php echo htmlspecialchars($_SESSION['nombre']); ?>. ¿Cuánto es <?php echo htmlspecialchars($numero1); ?> x <?php echo htmlspecialchars($numero2); ?>?</h3>
        <form action="index.php" method="post" id="formMultiplicacion">
            <input type="number" name="respuesta" id="respuesta" required>
            <!-- Inputs ocultos para enviar los números generados -->
            <input type="hidden" name="numero1" value="<?php echo htmlspecialchars($numero1); ?>">
            <input type="hidden" name="numero2" value="<?php echo htmlspecialchars($numero2); ?>">
            <br>
            <button type="submit">Responder</button>
        </form>
        <p>Respuestas correctas: <?php echo $_SESSION['correctas']; ?>, Incorrectas: <?php echo $_SESSION['incorrectas']; ?></p>
        <p>Monedas ganadas: <?php echo $_SESSION['monedas']; ?></p>
    </body>
    </html>
    <?php
}

// Función para mostrar el resultado
function mostrarResultado($numero1, $numero2, $respuestaUsuario, $resultadoCorrecto) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Resultado</title>
        <link rel="stylesheet" href="styles.css">
        <link rel="shortcut icon" href="./img/X.png" type="image/x-icon">
    </head>
    <body>
        <h1>Multiplicando Ando</h1>
        <?php if ($respuestaUsuario == $resultadoCorrecto): ?>
            <h2>¡Correcto, <?php echo htmlspecialchars($_SESSION['nombre']); ?>! <?php echo htmlspecialchars($numero1); ?> x <?php echo htmlspecialchars($numero2); ?> = <?php echo htmlspecialchars($respuestaUsuario); ?></h2>
            <img src="img/happy.png" alt="happy">
        <?php else: ?>
            <h2>Incorrecto, <?php echo htmlspecialchars($_SESSION['nombre']); ?>. <?php echo htmlspecialchars($numero1); ?> x <?php echo htmlspecialchars($numero2); ?> es <?php echo htmlspecialchars($resultadoCorrecto); ?>, no <?php echo htmlspecialchars($respuestaUsuario); ?>.</h2>
            <img src="img/sad.png" alt="sad">
        <?php endif; ?>
        <br><br>
        <div class="play">
            <form action="index.php" method="post">
                <!-- Botón para intentar otra vez -->
                <input type="hidden" name="retry" value="1">
                <button type="submit">Continúa</button>
            </form>
            <form action="index.php" method="post">
                <!-- Botón para finalizar y ver resumen -->
                <input type="hidden" name="finalizar" value="1">
                <button type="submit" style="background-color: #f00; color: #fff;">Finaliza</button>
            </form>
        </div>
    </body>
    </html>
    <?php
}

// Función para mostrar el resumen final
function mostrarResumenFinal() {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Resumen Final</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <h1>Resumen Final</h1>
        <h2>Resumen de tu partida, <?php echo htmlspecialchars($_SESSION['nombre']); ?>:</h2>
        <p>Respuestas correctas: <?php echo $_SESSION['correctas']; ?></p>
        <p>Respuestas incorrectas: <?php echo $_SESSION['incorrectas']; ?></p>
        <p>Monedas ganadas: <?php echo $_SESSION['monedas']; ?></p>
        <form action="index.php" method="post">
            <button type="submit">Volver a jugar</button>
        </form>
    </body>
    </html>
    <?php
}

// Función para verificar si el juego ha terminado
function verificarFinDeJuego() {
    $totalMultiplicacionesPosibles = 64; // 8x8 combinaciones de números del 2 al 9
    if (count($_SESSION['multiplicacionesRealizadas']) >= $totalMultiplicacionesPosibles) {
        mostrarResumenFinal();
        session_destroy(); // Limpiar sesión
        exit;
    }
}

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si se ha enviado el nombre del usuario
    if (isset($_POST['my_name']) && !empty(trim($_POST['my_name']))) {
        // Convertir la primera letra en mayúscula
        $_SESSION['nombre'] = ucfirst(strtolower(htmlspecialchars(trim($_POST['my_name'])))); // Guardar el nombre en la sesión
        $_SESSION['correctas'] = 0;  // Inicializar respuestas correctas
        $_SESSION['incorrectas'] = 0; // Inicializar respuestas incorrectas
        $_SESSION['monedas'] = 0; // Inicializar monedas
        $_SESSION['multiplicacionesRealizadas'] = []; // Inicializar el array de multiplicaciones realizadas
    } elseif (isset($_POST['my_name'])) {
        mostrarFormularioNombre("Por favor, ingresa un nombre válido."); // Mostrar error si el nombre está vacío
        exit;
    }

    // Si ya se tiene el nombre del usuario en la sesión
    if (isset($_SESSION['nombre'])) {
        // Si se ha enviado una respuesta de multiplicación
        if (isset($_POST['respuesta'])) {
            // Obtener los números y la respuesta del usuario de forma segura
            $numero1 = filter_input(INPUT_POST, 'numero1', FILTER_VALIDATE_INT);
            $numero2 = filter_input(INPUT_POST, 'numero2', FILTER_VALIDATE_INT);
            $respuestaUsuario = filter_input(INPUT_POST, 'respuesta', FILTER_VALIDATE_INT);
    
            if ($numero1 !== false && $numero2 !== false && $respuestaUsuario !== false) {
                // Calcular el resultado correcto
                $resultadoCorrecto = $numero1 * $numero2;
                // Actualizar contador de respuestas correctas e incorrectas
                if ($respuestaUsuario == $resultadoCorrecto) {
                    $_SESSION['correctas']++;

                    // Ganar una moneda por cada 5 respuestas correctas
                    if ($_SESSION['correctas'] % 5 == 0) {
                        $_SESSION['monedas']++;
                    }
                } else {
                    $_SESSION['incorrectas']++;

                    // Descontar una moneda por cada 3 respuestas incorrectas
                    if ($_SESSION['incorrectas'] % 3 == 0) {
                        $_SESSION['monedas']--;
                    }
                }
                // Verificar si se han realizado todas las multiplicaciones
                verificarFinDeJuego();

                // Mostrar el resultado con el nombre del usuario
                mostrarResultado($numero1, $numero2, $respuestaUsuario, $resultadoCorrecto);
            } else {
                echo "Datos inválidos proporcionados.";
            }
        } elseif (isset($_POST['retry']) && $_POST['retry'] == '1') {
            // Generar multiplicación única
            [$numero1, $numero2] = generarMultiplicacionUnica();
            // Mostrar el formulario de multiplicación con el nombre del usuario
            mostrarFormularioMultiplicacion($numero1, $numero2);
        } elseif (isset($_POST['finalizar']) && $_POST['finalizar'] == '1') {
            // Mostrar resumen final automáticamente
            mostrarResumenFinal();
            session_destroy(); // Limpiar sesión al finalizar
            exit;
        } else {
            // Generar multiplicación única
            [$numero1, $numero2] = generarMultiplicacionUnica();
            // Verificar si se han realizado todas las multiplicaciones
            verificarFinDeJuego();

            // Mostrar el formulario de multiplicación con el nombre del usuario
            mostrarFormularioMultiplicacion($numero1, $numero2);
        }
    } else {
        // Si no se ha enviado el nombre, mostrar el formulario de nombre
        mostrarFormularioNombre();
    }
} else {
    // Si no hay datos POST y no se ha definido el nombre del usuario en la sesión
    if (!isset($_SESSION['nombre'])) {
        mostrarFormularioNombre();
    } else {
        // Generar multiplicación única
        [$numero1, $numero2] = generarMultiplicacionUnica();
        mostrarFormularioMultiplicacion($numero1, $numero2);
    }
}
?>
