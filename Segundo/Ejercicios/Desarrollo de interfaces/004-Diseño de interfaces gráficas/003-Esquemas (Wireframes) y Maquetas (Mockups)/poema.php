<?php
$poemas = [
    'Romance Sonambulo' => [
        'titulo' => 'Romance Sonámbulo',
        'contenido' => "Verde que te quiero verde.<br>
Verde viento. Verdes ramas.<br>
El barco sobre la mar<br>
y el caballo en la montaña.<br>
Con la sombra en la cintura<br>
ella sueña en su baranda,<br>
verde carne, pelo verde,<br>
con ojos de fría plata.<br>
Verde que te quiero verde.<br>
Bajo la luna gitana,<br>
las cosas la están mirando<br>
y ella no puede mirarlas.",
        'tema' => 'Amor frustrado y muerte'
    ],
    'La Guitarra' => [
        'titulo' => 'La Guitarra',
        'contenido' => "Empieza el llanto<br>
de la guitarra.<br>
Se rompen las copas<br>
de la madrugada.<br>
Empieza el llanto<br>
de la guitarra.<br>
Es inútil callarla.<br>
Es imposible<br>
callarla.",
        'tema' => 'La pena y el destino trágico'
    ]
];

$titulo_url = isset($_GET['titulo']) ? $_GET['titulo'] : '';
$poema_actual = isset($poemas[$titulo_url]) ? $poemas[$titulo_url] : null;

if ($poema_actual) {
    echo "<div style='max-width: 800px; margin: 0 auto; background: var(--surface-color); padding: 2rem; border-radius: 12px; box-shadow: var(--card-shadow);'>";
    echo "<h2 style='text-align: center; color: var(--primary-color);'>" . $poema_actual['titulo'] . "</h2>";
    echo "<p style='font-style: italic; text-align: center; color: #aaa;'>Tema: " . $poema_actual['tema'] . "</p>";
    echo "<div style='font-size: 1.1rem; line-height: 1.8; margin-top: 2rem; padding-left: 1rem; border-left: 4px solid var(--secondary-color);'>";
    echo $poema_actual['contenido'];
    echo "</div>";
    echo "<div style='text-align: center; margin-top: 2rem;'><a href='index.php?p=actores' class='btn'>Volver a Poetas</a></div>";
    echo "</div>";
} else {
    echo "<h2>Poema no encontrado</h2>";
    echo "<p>Lo sentimos, no hemos podido encontrar el poema que buscas.</p>";
    echo "<a href='index.php?p=actores' class='btn'>Volver a la lista</a>";
}
?>
