<?php
$carpetaNombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
$carpetaRuta = "./descarga/" . $carpetaNombre;

try {
    if (!file_exists($carpetaRuta)) {
        mkdir($carpetaRuta, 0755, true);
        // Solo crea la carpeta sin mostrar mensaje aún
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['archivo'])) {
            $archivo = $_FILES['archivo'];
            $nombreArchivo = $archivo['name'];
            $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
            $tamanoMaximo = 10 * 1024 * 1024;

            if ($archivo['size'] > $tamanoMaximo) {
                throw new Exception("El archivo no puede ser mayor a 10MB.");
            }


            // Lista blanca de extensiones permitidas
            $extensionesPermitidas = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'];

            
            // Validación
            if (in_array($extension, $extensionesPermitidas)) {
                $rutaDestino = $carpetaRuta . '/' . $nombreArchivo;
                if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                $subido = true;
                $mensaje = "Archivo subido con éxito.";
            } else {
                throw new Exception("Error al subir el archivo.");
            }
        } else {
            throw new Exception("Tipo de archivo no permitido. Solo se aceptan PDF, Word, JPG o PNG.");
        }
    }
}

    if (isset($_POST['eliminarArchivo'])) {
        $archivoAEliminar = $_POST['eliminarArchivo'];
        $archivoRutaAEliminar = $carpetaRuta . '/' . $archivoAEliminar;

        if (file_exists($archivoRutaAEliminar)) {
            if (unlink($archivoRutaAEliminar)) {
                $mensaje = "Archivo '$archivoAEliminar' eliminado con éxito.";
            } else {
                throw new Exception("Error al eliminar el archivo.");
            }
        } else {
            throw new Exception("El archivo '$archivoAEliminar' no existe.");
        }
    }
} catch (Exception $e) {
    $mensaje = "Error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir archivos</title>
    <script src="parametro.js"></script>
    <link rel="stylesheet" href="estilo.css">
</head>

<body>
    <h1>Compartir archivos <sup class="beta">BETA</sup></h1>
    
<?php if (isset($mensaje)) : ?>
    <?php $esError = str_contains(strtolower($mensaje), 'error'); ?>
    <div id="mensaje-flotante" style="
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px;
        margin-top: 10px;
        background-color: <?= $esError ? '#ffe0e0' : '#e0ffe0' ?>;
        color: <?= $esError ? '#a00' : '#070' ?>;
        border: 1px solid <?= $esError ? '#a00' : '#0a0' ?>;
        border-radius: 8px;
        font-weight: bold;
    ">
        <?= $esError ? '❌' : '✅' ?> <?= $mensaje ?>
    </div>
<?php endif; ?>
    
    <div class="content">
        <h3>Sube tus archivos y comparte este enlace temporal: <span>ibu.pe/?nombre=<?php echo $carpetaNombre;?></span></h3>
        <div class="container">
            <div class="drop-area" id="drop-area">
                <form action="" id="form" method="POST" enctype="multipart/form-data">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" style="fill:#0730c5;transform: ;msFilter:;"><path d="M13 19v-4h3l-4-5-4 5h3v4z"></path><path d="M7 19h2v-2H7c-1.654 0-3-1.346-3-3 0-1.404 1.199-2.756 2.673-3.015l.581-.102.192-.558C8.149 8.274 9.895 7 12 7c2.757 0 5 2.243 5 5v1h1c1.103 0 2 .897 2 2s-.897 2-2 2h-3v2h3c2.206 0 4-1.794 4-4a4.01 4.01 0 0 0-3.056-3.888C18.507 7.67 15.56 5 12 5 9.244 5 6.85 6.611 5.757 9.15 3.609 9.792 2 11.82 2 14c0 2.757 2.243 5 5 5z"></path></svg> <br>
                    <input type="file" class="file-input" name="archivo" id="archivo" onchange="document.getElementById('form').submit()">
                    <label> Arrastra tus archivos aquí<br>o</label>
                    <p><b>Abre el explorador</b></p> 
                    
                </form>
            </div>

            <div class="container2">
               

                <div id="file-list" class="pila">
                    <?php
                    $targetDir = $carpetaRuta;

                    $files = scandir($targetDir);
                    $files = array_diff($files, array('.', '..'));

                    if (count($files) > 0) {
                        echo " <h3 style='margin-bottom:10px;'>Archivos Subidos:</h3>";

                        foreach ($files as $file) {
                            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $rutaArchivo = "$carpetaRuta/$file";
                        
                            echo "<div class='archivos_subidos'>
                                <div><a href='$rutaArchivo' download class='boton-descargar'>$file</a></div>
                                <div>
                                    <form action='' method='POST' style='display:inline;'>
                                        <input type='hidden' name='eliminarArchivo' value='$file'>
                                        <button type='submit' class='btn_delete'>
                                            <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-trash' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                                <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                                <path d='M4 7l16 0' />
                                                <path d='M10 11l0 6' />
                                                <path d='M14 11l0 6' />
                                                <path d='M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12' />
                                                <path d='M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3' />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>";
                        
                            // Previsualización solo para PDF e imágenes
                            if ($extension === 'pdf') {
                                echo "<div style='margin-top:10px;'>
                                        <h4>Previsualización:</h4>
                                        <iframe src='$rutaArchivo' width='100%' height='500px' style='border:1px solid #ccc; border-radius:10px;'></iframe>
                                      </div>";
                            } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                                echo "<div style='margin-top:10px;'>
                                        <h4>Previsualización:</h4>
                                        <img src='$rutaArchivo' style='max-width:100%; height:auto; border-radius:10px; border:1px solid #ccc;' />
                                      </div>";
                            }
                        }
                    } else {
                        echo "No se han subido archivos.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- <script src="parametro.js"></script> -->

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const mensaje = document.getElementById('mensaje-flotante');
        if (mensaje) {
            setTimeout(() => {
                mensaje.style.transition = "opacity 0.5s ease-out";
                mensaje.style.opacity = "0";
                setTimeout(() => mensaje.remove(), 500); // Elimina el div después de desvanecer
            }, 4000); // 4 segundos visibles
        }
    });
</script>

</body>

</html>
